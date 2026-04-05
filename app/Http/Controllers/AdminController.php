<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\depot;
use App\Models\paiement;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Affiche le tableau de bord administrateur (statistiques et graphiques).
     */
    public function dashboard()
    {
        // 1. Chiffre d'affaires total
        $revenusTotal = paiement::sum('montant');

        // 2. Reste à recouvrer
        $totalAttendu = depot::sum('prix_total');
        $totalNonPaye = max(0, $totalAttendu - $revenusTotal);

        // 3. Total clients
        $totalClients = \App\Models\User::where('role', 'client')->count();

        // 4. Clients actifs (ayant au moins un dépôt)
        $clientsActifs = \App\Models\User::where('role', 'client')->whereHas('depots')->count();

        // 5. Volume global (total des dépôts)
        $depotsCount = depot::count();

        // 6. Dépôts prêts à retirer
        $pretCount = depot::where('etat', 'pret')->count();

        // 7. Derniers dépôts (limité à 4)
        $latestDepots = depot::with('client')->latest()->take(4)->get();

        // 8. Données pour graphiques mensuels de l'année en cours
        $revenusData = array_fill(1, 12, 0);
        $depotsData = array_fill(1, 12, 0);

        for ($i = 1; $i <= 12; $i++) {
            $revenusData[$i] = paiement::whereYear('date_paiement', Carbon::now()->year)
                                       ->whereMonth('date_paiement', $i)
                                       ->sum('montant');
            
            $depotsData[$i] = depot::whereYear('date_depot', Carbon::now()->year)
                                   ->whereMonth('date_depot', $i)
                                   ->count();
        }

        $chartRevenus = array_values($revenusData);
        $chartDepots = array_values($depotsData);

        // 9. Chargement de la vue
        return view('dashboard', compact(
            'revenusTotal', 
            'totalNonPaye', 
            'totalClients', 
            'clientsActifs', 
            'depotsCount', 
            'pretCount', 
            'latestDepots',
            'chartRevenus',
            'chartDepots'
        ));
    }
}
