<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Depot;
use App\Models\Paiement;
use Illuminate\Support\Facades\Log;

class G2TPayController extends Controller
{
    /**
     * Initialise le paiement G2TPay avec un montant choisi par le client.
     */
    public function initier(Request $request, $id)
    {
        $depot = Depot::findOrFail($id);

        // Validation du montant saisi par le client
        $request->validate([
            'montant' => 'required|numeric|min:100|max:' . $depot->reste_a_payer,
        ], [
            'montant.min' => 'Le montant minimum est de 100 F.',
            'montant.max' => 'Le montant ne peut pas dépasser le reste à payer (' . $depot->reste_a_payer . ' F).',
        ]);

        $montantAPayer = intval($request->montant);
        $apiKey = env('G2TPAY_API_KEY');
        $baseUrl = env('G2TPAY_BASE_URL', 'https://g2tpay.net/integrate/pay');

        // Générer une référence unique pour cette tentative de paiement spécifique
        $transactionReference = 'WP-' . now()->format('YmdHis') . '-' . $depot->id;

        // Construire l'URL de redirection G2TPay selon leur documentation
        $params = [
            'api_key'     => $apiKey,
            'amount'      => $montantAPayer,
            'description' => 'Paiement Facture ' . $depot->reference,
            'reference'   => $transactionReference, 
            'return_url'  => route('client.paiement.retour', [
                'depot_id' => $depot->id,
                'amount_captured' => $montantAPayer // On transmet le montant attendu pour le retour
            ]),
            'email'       => auth()->user()->email,
            'phone'       => auth()->user()->telephone,
        ];

        // Journalisation pour le débogage
        Log::info("Initiation Paiement G2TPay - Depot: " . $depot->id . " - Réf: " . $transactionReference . " - Montant: " . $montantAPayer);

        $redirectUrl = $baseUrl . '?' . http_build_query($params);

        return redirect()->away($redirectUrl);
    }

    /**
     * Gère le retour du client depuis G2TPay après la transaction.
     */
    public function retour(Request $request)
    {
        $status = strtolower($request->query('status', ''));
        $depotId = $request->query('depot_id');
        $montantCapture = intval($request->query('amount_captured', 0));

        if (!$depotId) {
            return redirect()->route('client.dashboard')->with('error', 'Paramètres de retour invalides.');
        }

        $depot = Depot::findOrFail($depotId);

        // Si le paiement est un succès
        if ($status === 'success' || $status === 'succès' || $status === 'successful') {
            
            // On vérifie si ce paiement exact (montant + dépôt) n'a pas déjà été enregistré aujourd'hui
            // pour éviter les doublons en cas de rafraîchissement de page par le client.
            $dejaPaye = Paiement::where('mode_paiement', 'g2tpay')
                                ->where('date_paiement', now()->toDateString())
                                ->where('montant', $montantCapture)
                                ->where('depot_id', $depotId)
                                ->exists();

            if (!$dejaPaye && $montantCapture > 0) {
                // Création de l'enregistrement de paiement
                Paiement::create([
                    'depot_id' => $depotId,
                    'montant' => $montantCapture,
                    'mode_paiement' => 'g2tpay', // Identifiant de paiement en ligne
                    'date_paiement' => now()->toDateString(),
                ]);

                // IMPORTANT : On rafraîchit les données du dépôt pour que le calcul 
                // du "reste_a_payer" intègre le nouveau paiement immédiatement.
                $depot->refresh();

                // Mise à jour du statut global du dépôt
                if ($depot->reste_a_payer <= 0) {
                    $depot->update(['etat_paiement' => 'payé']);
                } else {
                    $depot->update(['etat_paiement' => 'partiel']);
                }

                return redirect()->route('client.dashboard')->with('success', 'Votre paiement de ' . $montantCapture . ' F a été validé avec succès !');
            } else {
                return redirect()->route('client.dashboard')->with('success', 'Votre paiement a déjà été pris en compte.');
            }

        } elseif ($status === 'failed' || $status === 'echec') {
            return redirect()->route('client.dashboard')->with('error', 'Le paiement a échoué. Veuillez réessayer.');
        } elseif ($status === 'expired') {
            return redirect()->route('client.dashboard')->with('error', 'La session de paiement a expiré.');
        }

        return redirect()->route('client.dashboard')->with('error', 'Paiement annulé ou non certifié.');
    }
}
