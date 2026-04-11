<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Depot;
use App\Models\User;
use App\Models\Linge;

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
        // Recherche par Code de Retrait (prioritaire et directe)
        if ($request->filled('code_retrait')) {
            $query->where('code_retrait', 'LIKE', '%' . strtoupper(trim($request->code_retrait)) . '%');
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

            // Génération du code de retrait UNIQUE pour ce dépôt
            $codeRetrait = 'WP-' . strtoupper(Str::random(5));
            // S'assurer de l'unicité
            while (Depot::where('code_retrait', $codeRetrait)->exists()) {
                $codeRetrait = 'WP-' . strtoupper(Str::random(5));
            }
            
            $depot->update([
                'prix_total' => $totalDepot,
                'code_retrait' => $codeRetrait
            ]);

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

            DB::commit();

            // Actions après-vente (Post-Commit) pour améliorer la performance
            try {
                $client = User::find($request->client_id);
                if ($client && $client->email) {
                    // Envoi du mail de confirmation avec le code de retrait intégré dans le dépôt
                    Mail::to($client->email)->queue(new DepotConfirmationMail($depot));
                    
                    // Si un paiement initial a été effectué
                    if (isset($montantPaye) && $montantPaye > 0) {
                        Mail::to($client->email)->queue(new PaiementRecuMail($depot, $montantPaye, $request->mode_paiement));
                    }
                }

                // Notification interne pour le client
                \App\Models\Notification::create([
                    'user_id' => $depot->client_id,
                    'message' => 'Votre dépôt #' . str_pad($depot->id, 5, '0', STR_PAD_LEFT) . ' a bien été enregistré. Code de retrait : ' . $codeRetrait,
                    'date_envoi' => now(),
                    'lu' => false,
                ]);

                $this->notificationService->sendToAdmins("Nouveau dépôt créé : Dépôt #{$depot->id}");
                $this->notificationService->sendToTechnicians("Nouveau dépôt à traiter : Nouveau dépôt #{$depot->id} assigné.");

            } catch (\Exception $postException) {
                // On log l'erreur mais le dépôt est déjà validé en DB
                \Log::error("Erreur post-enregistrement dépôt #{$depot->id} : " . $postException->getMessage());
            }

            // Redirection vers la facture pour impression immédiate
            return redirect()->route('depots.facture', $depot->id)->with('success', 'Dépôt enregistré avec succès ! Veuillez imprimer le reçu.');

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

    /**
     * Recherche instantanée d'un dépôt par son code de retrait (AJAX).
     */
    public function rechercheParCode(Request $request)
    {
        $code = strtoupper(trim($request->input('code', '')));

        if (strlen($code) < 2) {
            return response()->json(['depot' => null]);
        }

        $depot = Depot::with(['client', 'linges.service', 'paiements'])
            ->where('code_retrait', 'LIKE', "%{$code}%")
            ->first();

        if (!$depot) {
            return response()->json(['depot' => null]);
        }

        return response()->json([
            'depot' => [
                'id'            => $depot->id,
                'reference'     => $depot->reference,
                'code_retrait'  => $depot->code_retrait,
                'etat'          => $depot->etat,
                'etat_paiement' => $depot->etat_paiement,
                'prix_total'    => number_format($depot->prix_total, 0, ',', ' '),
                'reste_a_payer' => number_format($depot->reste_a_payer, 0, ',', ' '),
                'date_depot'    => \Carbon\Carbon::parse($depot->date_depot)->format('d/m/Y H:i'),
                'date_retrait'  => \Carbon\Carbon::parse($depot->date_retrait_prevue)->format('d/m/Y H:i'),
                'client'        => $depot->client ? $depot->client->nom . ' ' . $depot->client->prenom : 'Inconnu',
                'telephone'     => $depot->client->telephone ?? '-',
                'articles'      => $depot->linges->map(fn($l) => $l->quantite . 'x ' . ($l->service->libelle ?? '')),
                'url_facture'   => route('depots.facture', $depot->id),
                'url_detail'    => route('depots.show', $depot->id),
            ]
        ]);
    }
}
