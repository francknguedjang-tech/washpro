<?php

namespace App\Http\Controllers;

use App\Models\Depot;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaiementRecuMail;
use App\Services\NotificationService;

class PaiementController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Affiche la liste de tous les paiements enregistrés.
     * Permet également de filtrer les paiements par recherche de texte (q), 
     * par date (date) ou par mode de paiement (mode).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // On initialise la requête en chargeant les paiements avec leurs dépôts et clients associés (Eager Loading)
        // Cela permet d'optimiser les performances de la base de données.
        $query = Paiement::with(['depot', 'depot.client']);
        
        // 1. Filtrage par mot-clé (numéro de dépôt, nom, prénom ou téléphone du client)
        if ($request->has('q') && $request->q != '') {
            $q = $request->q;
            $query->whereHas('depot', function($qQuery) use ($q) {
                $qQuery->where('reference', 'like', "%{$q}%")
                      ->orWhereHas('client', function($cQuery) use ($q) {
                          $cQuery->where('nom', 'like', "%{$q}%")
                                 ->orWhere('prenom', 'like', "%{$q}%")
                                 ->orWhere('telephone', 'like', "%{$q}%");
                      });
            });
        }
        
        // 2. Filtrage par date de paiement exacte
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('date_paiement', $request->date);
        }
        
        // 3. Filtrage par moyen de paiement (Espèces, Mobile Money, etc.)
        if ($request->has('mode') && $request->mode != '') {
            $query->where('mode_paiement', $request->mode);
        }
        
        // On exécute la requête, en triant du paiement le plus récent au plus ancien
        $paiements = $query->orderBy('date_paiement', 'desc')->get();
        return view('paiements.index', compact('paiements'));
    }

    public function depotPaiements($id)
    {
        $depot = Depot::findOrFail($id);
        $paiements = $depot->paiements()->orderBy('date_paiement', 'desc')->get();
        return view('paiements.depot_paiements', compact('depot', 'paiements'));
    }

    /**
     * Enregistre un nouveau paiement partiel ou total pour un dépôt spécifique.
     * Met également à jour le statut du dépôt, envoie un reçu par email au client
     * et génère des notifications système.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // 1. Validation stricte des données venant du formulaire
        $request->validate([
            'depot_id' => 'required|exists:depots,id', // Le dépôt doit exister
            'montant' => 'required|numeric|min:1',     // Le montant doit être valide (>0)
            'mode_paiement' => 'required|in:cache,orange_money,mobile_money,g2tpay', // Seuls ces modes sont acceptés
        ]);

        $depot = Depot::findOrFail($request->depot_id);
        
        // 2. Vérification de sécurité : le client ne peut pas payer plus que ce qu'il doit
        // C'est une protection très importante contre les erreurs de frappe (ex: taper 50000 au lieu de 5000)
        if ($request->montant > $depot->reste_a_payer) {
            return back()->with('error', 'Le montant saisi depasse le reste à payer.');
        }

        // 3. Création officielle du paiement en base de données
        Paiement::create([
            'depot_id' => $depot->id,
            'montant' => $request->montant,
            'mode_paiement' => $request->mode_paiement,
            'date_paiement' => now()->toDateString(),
        ]);
        
        // On recalcule l'état total du dépôt (S'il vient d'être totalement payé, la fonction le mettra à jour)
        $this->updateDepotPaymentStatus($depot);

        // 4. Envoi automatique du reçu dématérialisé (Email)
        if ($depot->client && $depot->client->email) {
            try {
                // On met à jour les données du dépôt en mémoire avant l'envoi
                $depot->refresh(); 
                Mail::to($depot->client->email)->send(new PaiementRecuMail($depot, $request->montant, $request->mode_paiement));
            } catch (\Exception $e) {
                \Log::error("Échec de l'envoi de l'email de reçu de paiement : " . $e->getMessage());
            }
        }

        // 5. Création d'une notification visible sur l'espace client "WashPro"
        \App\Models\Notification::create([
            'user_id' => $depot->client_id,
            'message' => 'Un paiement de ' . number_format($request->montant, 0, ',', ' ') . ' F a été enregistré sur votre dépôt #' . str_pad($depot->id, 5, '0', STR_PAD_LEFT) . '.',
            'date_envoi' => now(),
            'lu' => false,
        ]);

        // 6. Alertes à l'équipe (Administrateurs)
        $this->notificationService->sendToAdmins("Paiement enregistré : Un paiement de {$request->montant} a été ajouté au dépôt #{$depot->id}.");
        
        $depot->refresh();
        // Si la facture n'est pas encore réglée à 100%, on prévient aussi les réceptionnistes
        if ($depot->reste_a_payer > 0) {
            $this->notificationService->sendToAdmins("Paiement partiel : Le dépôt #{$depot->id} n'est pas entièrement payé.");
            $this->notificationService->sendToReceptionists("Paiement partiel restant : Le dépôt #{$depot->id} a un solde restant.");
        }

        return back()->with('success', 'Paiement enregistré avec succès.');
    }

    /**
     * Raccourci d'action : Marque un dépôt comme "Totalement payé" en espèces (cache).
     * Ceci est utile pour les employés à la caisse pour aller vite au comptoir.
     *
     * @param  int  $id L'identifiant (ID) du dépôt
     * @return \Illuminate\Http\RedirectResponse
     */
    public function marquerPaye($id)
    {
        $depot = Depot::findOrFail($id);
        $reste = $depot->reste_a_payer;
        
        // S'il reste effectivement de l'argent à payer sur cette facture
        if ($reste > 0) {
            // 1. On crée le paiement en "cache" pour le montant exact restant
            Paiement::create([
                'depot_id' => $depot->id,
                'montant' => $reste,
                'mode_paiement' => 'cache',
                'date_paiement' => now()->toDateString(),
            ]);
            
            // 2. On met à jour le statut du dépôt (il passera logiquement à "Payé")
            $this->updateDepotPaymentStatus($depot);
            
            // 3. Envoi du reçu final par email au client
            if ($depot->client && $depot->client->email) {
                try {
                    $depot->refresh(); 
                    Mail::to($depot->client->email)->send(new PaiementRecuMail($depot, $reste, 'cache'));
                } catch (\Exception $e) {
                    \Log::error("Échec de l'envoi de l'email de reçu de paiement complet: " . $e->getMessage());
                }
            }

            // 4. Notification pour le tableau de bord du client
            \App\Models\Notification::create([
                'user_id' => $depot->client_id,
                'message' => 'Un paiement de ' . number_format($reste, 0, ',', ' ') . ' F a été enregistré sur votre dépôt #' . str_pad($depot->id, 5, '0', STR_PAD_LEFT) . '.',
                'date_envoi' => now(),
                'lu' => false,
            ]);

            // 5. Alerte à l'administration
            $this->notificationService->sendToAdmins("Paiement enregistré : Le dépôt #{$depot->id} a été marqué comme totalement payé.");

            return back()->with('success', 'Dépôt marqué comme payé.');
        }

        // Si la facture était déjà à 0 franc restant
        return back()->with('info', 'Ce dépôt est déjà payé.');
    }
    
    private function updateDepotPaymentStatus(Depot $depot)
    {
        $reste = $depot->reste_a_payer; 
        $total_paye = $depot->paiements()->sum('montant');
        
        if ($total_paye <= 0) {
            $depot->update(['etat_paiement' => 'non payé']);
        } else if ($reste <= 0) {
            $depot->update(['etat_paiement' => 'payé']);
        } else {
            $depot->update(['etat_paiement' => 'partiel']);
        }
    }

    public function destroy($id)
    {
        $paiement = Paiement::findOrFail($id);
        $depot = $paiement->depot;
        
        $paiement->delete();
        
        $this->updateDepotPaymentStatus($depot);
        
        return back()->with('success', 'Paiement supprimé avec succès.');
    }

    public function genererRecu($id)
    {
        $depot = Depot::with(['client', 'paiements', 'service', 'receptionniste'])->findOrFail($id);
        return view('paiements.recu', compact('depot'));
    }
}
