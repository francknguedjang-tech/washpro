<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Depot;
use App\Models\Paiement;
use Illuminate\Support\Facades\Log;

class G2TPayController extends Controller
{
    /**
     * Initialise le paiement G2TPay et redirige le client.
     */
    public function initier($id)
    {
        $depot = Depot::findOrFail($id);

        if ($depot->reste_a_payer <= 0) {
            return redirect()->back()->with('error', 'Ce dépôt est déjà entièrement payé.');
        }

        $apiKey = env('G2TPAY_API_KEY');
        $baseUrl = env('G2TPAY_BASE_URL', 'https://g2tpay.net/integrate/pay');

        // Générer une référence unique pour cette tentative de paiement spécifique
        $transactionReference = 'WP-' . now()->format('YmdHis') . '-' . $depot->id;

        // Construire l'URL de redirection G2TPay selon leur documentation
        $params = [
            'api_key'     => $apiKey,
            'amount'      => intval($depot->reste_a_payer), // Toujours forcer l'entier pour G2TPay/FCFA
            'description' => 'Facture ' . $depot->reference,
            'reference'   => $transactionReference, // Référence de transaction pour G2TPay
            'return_url'  => route('client.paiement.retour', ['depot_id' => $depot->id]),
            'email'       => auth()->user()->email, // Optionnel mais aide G2TPay
            'phone'       => auth()->user()->telephone, // Optionnel mais aide G2TPay
        ];

        // LOG pour débogage (Visible dans storage/logs/laravel.log)
        Log::info("Initiation Paiement G2TPay - Depot: " . $depot->id . " - Réf: " . $transactionReference);

        $redirectUrl = $baseUrl . '?' . http_build_query($params);

        // Envoyer l'utilisateur vers la page de G2TPay
        return redirect()->away($redirectUrl);
    }

    /**
     * Gère le retour du client depuis G2TPay après la transaction.
     */
    public function retour(Request $request)
    {
        // G2TPay passe notamment les champs status, message_id, payment_id
        $status = strtolower($request->query('status', ''));
        $depotId = $request->query('depot_id');
        $paymentId = $request->query('payment_id', 'INCONNU');

        if (!$depotId) {
            return redirect()->route('client.dashboard')->with('error', 'Paramètres de retour invalides.');
        }

        $depot = Depot::findOrFail($depotId);

        // Analyse du statut de la transaction
        if ($status === 'success' || $status === 'succès' || $status === 'successful') {
            
            // Protection : Vérifier qu'on a pas déjà enregistré ce paiement
            $dejaPaye = Paiement::where('mode_paiement', 'g2tpay')
                                ->where('date_paiement', now()->toDateString())
                                ->where('montant', $depot->reste_a_payer)
                                ->where('depot_id', $depotId)
                                ->exists();

            if (!$dejaPaye && $depot->reste_a_payer > 0) {
                // Enregistrer ce paiement
                $montantCapture = $depot->reste_a_payer;
                
                Paiement::create([
                    'depot_id' => $depotId,
                    'montant' => $montantCapture,
                    'mode_paiement' => 'g2tpay', // Identifiant de paiement en ligne
                    'date_paiement' => now()->toDateString(),
                ]);

                // Actualiser le statut global du dépôt
                $reste = $depot->prix_total - ($depot->paiements()->sum('montant') + $montantCapture);
                if ($reste <= 0) {
                    $depot->update(['etat_paiement' => 'payé']);
                } else {
                    $depot->update(['etat_paiement' => 'partiel']);
                }

                return redirect()->route('client.dashboard')->with('success', 'Paiement en ligne validé avec succès ! Merci de votre confiance.');
            } else {
                return redirect()->route('client.dashboard')->with('success', 'Votre paiement a déjà été validé.');
            }

        } elseif ($status === 'failed' || $status === 'echec') {
            return redirect()->route('client.dashboard')->with('error', 'Le paiement a échoué. Veuillez réessayer.');
        } elseif ($status === 'expired') {
            return redirect()->route('client.dashboard')->with('error', 'La session de paiement a expiré.');
        }

        // Cas par défaut (Status invalide ou Annulation par l'utilisateur)
        return redirect()->route('client.dashboard')->with('error', 'Paiement annulé ou non certifié.');
    }
}
