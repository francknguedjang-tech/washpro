<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\depot;
use App\Models\User;
use App\Models\linge;
use App\Models\code_acces;
use App\Mail\DepotConfirmationMail;
use App\Mail\DepotStatusUpdatedMail;
use App\Mail\PaiementRecuMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Services\NotificationService;

class DepotController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Affiche la liste globale de tous les dépôts avec options de filtrage 
     * (par date, statut, ou recherche générale).
     */
    public function index(Request $request)
    {
        $query = Depot::with(['client', 'linges.service']);

        if ($request->filled('date')) {
            $query->whereDate('date_depot', $request->date);
        }
        if ($request->filled('status')) {
            $query->where('etat', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('client', function($cq) use ($search) {
                    $cq->where('nom', 'LIKE', "%{$search}%")
                      ->orWhere('prenom', 'LIKE', "%{$search}%")
                      ->orWhere('telephone', 'LIKE', "%{$search}%");
                })->orWhere('id', 'LIKE', "%{$search}%");
            });
        }

        $depots = $query->latest()->paginate(10);
        return view('admin.depots.index', compact('depots'));
    }

    /**
     * Affiche les détails complets d'un dépôt spécifique (linges, client, réceptionniste, paiements).
     */
    public function show($id)
    {
        $depot = Depot::with(['client', 'linges.service', 'receptionniste', 'paiements'])->findOrFail($id);
        return view('admin.depots.show', compact('depot'));
    }

    /**
     * Affiche le formulaire de modification d'un dépôt existant.
     */
    public function edit($id)
    {
        $depot = Depot::with('linges')->findOrFail($id);
        $services = Service::all();
        return view('admin.depots.edit', compact('depot', 'services'));
    }

    /**
     * Met à jour le contenu d'un dépôt (articles, quantités) et recalcule le prix total.
     * Utilise une transaction pour garantir l'intégrité de la base de données.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'date_retrait' => 'required|date',
            'articles' => 'required|array|min:1',
            'articles.*.description' => 'required|string',
            'articles.*.service_id' => 'required|exists:services,id',
            'articles.*.quantite' => 'required|numeric|min:0.1',
        ]);

        $depot = Depot::findOrFail($id);
        
        DB::beginTransaction();
        try {
            $totalDepot = 0;
            $depot->update([
                'date_retrait_prevue' => $request->date_retrait,
            ]);

            // Simple approach: Delete old linges and recreate them
            // This avoids complex diffing logic
            $depot->linges()->delete();

            foreach ($request->articles as $item) {
                $service = Service::find($item['service_id']);
                $quantite = floatval($item['quantite']);
                $prixUnitaire = $service->prix_unitaire;
                $montantLigne = 0;

                if (str_contains(strtolower($service->unite), 'kg')) {
                    $entier = floor($quantite);
                    $decimale = round($quantite - $entier, 2);
                    if ($decimale > 0.6) {
                        $montantLigne = ceil($quantite) * $prixUnitaire;
                    } else if ($decimale > 0) {
                        $montantLigne = (ceil($quantite) * $prixUnitaire) - 500;
                    } else {
                        $montantLigne = $quantite * $prixUnitaire;
                    }
                } else {
                    $montantLigne = $quantite * $prixUnitaire;
                }

                Linge::create([
                    'description' => $item['description'],
                    'quantite' => $quantite,
                    'depot_id' => $depot->id,
                    'service_id' => $service->id,
                    'prix_unitaire' => $prixUnitaire
                ]);

                $totalDepot += $montantLigne;
            }

            $depot->update(['prix_total' => $totalDepot]);

            $this->notificationService->sendToTechnicians("Modification urgente : Le dépôt #{$depot->id} a été modifié.");

            DB::commit();
            return redirect()->route('depots.index')->with('success', 'Dépôt mis à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur : ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Supprime définitivement un dépôt. Action restreinte aux Administrateurs.
     */
    public function destroy($id)
    {
        if (Auth::user()->role != 'admin') {
            return back()->with('error', 'Action non autorisée.');
        }

        $depot = Depot::findOrFail($id);
        // Supprimer toutes les liaisons (articles et paiements) pour garder la BDD propre
        $depot->linges()->delete();
        $depot->paiements()->delete();
        $depot->delete();

        $this->notificationService->sendToAdmins("Anomalie : Le dépôt #{$id} a été supprimé !");

        return redirect()->route('depots.index')->with('success', 'Dépôt supprimé.');
    }

    /**
     * Affiche le formulaire de création d'un tout nouveau dépôt (réception).
     */
    public function create()
    {
        $services = Service::all();
        return view('admin.depots.create', compact('services'));
    }

    /**
     * Enregistre le nouveau dépôt, les articles associés, génère un code sécurisé
     * et envoie un email de confirmation de facture au client.
     */
    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:users,id',
            'date_retrait' => 'required|date|after:now',
            'articles' => 'required|array|min:1',
            'articles.*.description' => 'required|string',
            'articles.*.service_id' => 'required|exists:services,id',
            'articles.*.quantite' => 'required|numeric|min:0.1',
            'montant_paye' => 'nullable|numeric|min:0',
            'mode_paiement' => 'nullable|in:cache,orange_money,mobile_money',
        ]);

        DB::beginTransaction();
        try {
            $totalDepot = 0;

            $depot = Depot::create([
                'date_depot' => now(),
                'etat' => 'en cours',
                'prix_total' => 0,
                'poid' => 0,
                'service_id' => $request->articles[0]['service_id'] ?? 1,
                'client_id' => $request->client_id,
                'receptionniste_id' => Auth::id(),
                'date_retrait_prevue' => $request->date_retrait,
            ]);

            foreach ($request->articles as $item) {
                $service = Service::find($item['service_id']);
                $quantite = floatval($item['quantite']);
                $prixUnitaire = $service->prix_unitaire;
                $montantLigne = 0;

                if (str_contains(strtolower($service->unite), 'kg')) {
                    $entier = floor($quantite);
                    $decimale = round($quantite - $entier, 2);
                    
                    if ($decimale > 0.6) {
                        $montantLigne = ceil($quantite) * $prixUnitaire;
                    } else if ($decimale > 0) {
                        $montantLigne = (ceil($quantite) * $prixUnitaire) - 500;
                    } else {
                        $montantLigne = $quantite * $prixUnitaire;
                    }
                } else {
                    $montantLigne = $quantite * $prixUnitaire;
                }

                Linge::create([
                    'description' => $item['description'],
                    'quantite' => $quantite,
                    'depot_id' => $depot->id,
                    'service_id' => $service->id,
                    'prix_unitaire' => $prixUnitaire
                ]);

                $totalDepot += $montantLigne;
            }

            $depot->update(['prix_total' => $totalDepot]);

            // Enregistrement d'un paiement immédiat si fourni
            if ($request->filled('montant_paye') && $request->montant_paye > 0) {
                $montantPaye = floatval($request->montant_paye);
                
                // Eviter de payer plus que le total de maniere stricte
                if ($montantPaye > $totalDepot) {
                    throw new \Exception('Le montant saisi. ('.$montantPaye.' F) depasse le total de la facture ('.$totalDepot.' F).');
                }
                
                \App\Models\Paiement::create([
                    'depot_id' => $depot->id,
                    'montant' => $montantPaye,
                    'mode_paiement' => $request->mode_paiement ?? 'cache',
                    'date_paiement' => now()->toDateString(),
                ]);

                // Mise à jour de l'état de paiement
                $reste = $totalDepot - $montantPaye;
                if ($reste <= 0) {
                    $depot->update(['etat_paiement' => 'payé']);
                } else {
                    $depot->update(['etat_paiement' => 'partiel']);
                }
            }

            $client = User::find($request->client_id);
            $codeStr = null;
            if (!$client->password_changed) {
                $code = Code_acces::where('user_id', $client->id)->where('is_used', false)->first();
                if (!$code) {
                    $codeStr = strtoupper(Str::random(6));
                    Code_acces::create([
                        'user_id' => $client->id,
                        'code' => $codeStr,
                        'is_used' => false
                    ]);
                } else {
                    $codeStr = $code->code;
                }
            }

            if ($client->email) {
                try {
                    Mail::to($client->email)->queue(new DepotConfirmationMail($depot, $codeStr));
                    
                    // Si un paiement initial a été effectué
                    $montantPaye = floatval($request->montant_paye ?? 0);
                    if ($montantPaye > 0) {
                        $depot->refresh();
                        Mail::to($client->email)->queue(new PaiementRecuMail($depot, $montantPaye, $request->mode_paiement));
                    }
                } catch (\Exception $mailException) {
                    // Log the error but don't fail the transaction
                    \Log::error("Failed to send Emails for Depot : " . $mailException->getMessage());
                }
            }

            DB::commit();

            // Notification for the client
            \App\Models\Notification::create([
                'user_id' => $depot->client_id,
                'message' => 'Votre dépôt #' . str_pad($depot->id, 5, '0', STR_PAD_LEFT) . ' a bien été enregistré. Merci de votre confiance !',
                'date_envoi' => now(),
                'lu' => false,
            ]);

            $this->notificationService->sendToAdmins("Nouveau dépôt créé : Dépôt #{$depot->id}");
            $this->notificationService->sendToTechnicians("Nouveau dépôt à traiter : Nouveau dépôt #{$depot->id} assigné.");

            $dashboardRoute = Auth::user()->role === 'receptionniste' ? 'receptionniste.dashboard' : 'admin.dashboard';
            return redirect()->route($dashboardRoute)->with('success', 'Dépôt enregistré avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur : ' . $e->getMessage()])->withInput();
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $depot = Depot::findOrFail($id);
        $oldStatus = $depot->etat;
        $newStatus = $request->status;

        $depot->update(['etat' => $newStatus]);

        if ($newStatus == 'pret' && $oldStatus != 'pret') {
            \App\Models\Notification::create([
                'user_id' => $depot->client_id,
                'message' => "Votre dépôt #" . str_pad($depot->id, 5, '0', STR_PAD_LEFT) . " est prêt !",
                'date_envoi' => now(),
                'lu' => false,
            ]);
            
            // Envoyer l'email
            if ($depot->client && $depot->client->email) {
                try {
                    $depot->refresh();
                    Mail::to($depot->client->email)->queue(new DepotStatusUpdatedMail($depot));
                } catch (\Exception $e) {
                    \Log::error("Échec de l'envoi de l'email de statut: " . $e->getMessage());
                }
            }

            $this->notificationService->sendToReceptionists("Dépôt prêt : Le dépôt #{$depot->id} est prêt pour retrait.");
            $this->notificationService->sendToAdmins("Dépôt prêt : Le dépôt #{$depot->id} est prêt pour retrait.");
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Statut mis à jour']);
        }

        return redirect()->back()->with('success', 'Statut mis à jour.');
    }

    public function genererFacture($id)
    {
        $depot = Depot::with(['client', 'linges.service', 'paiements', 'receptionniste'])->findOrFail($id);
        return view('admin.depots.facture', compact('depot'));
    }
}
