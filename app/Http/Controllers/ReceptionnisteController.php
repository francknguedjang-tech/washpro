<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\depot;
use App\Models\User;
use App\Models\Paiement;
use Illuminate\Support\Facades\DB;

class ReceptionnisteController extends Controller
{
    /**
     * Constructeur pour vérifier l'authentification et limiter l'accès 
     * aux utilisateurs ayant le rôle "réceptionniste".
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:receptionniste']);
    }

    /**
     * Affiche le tableau de bord spécifique à la réceptionniste.
     * Fournit un aperçu de l'activité du jour, les clients en attente de paiement, 
     * et les derniers dépôts enregistrés.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function dashboard()
    {
        $depotsCount = Depot::count();
        $clientsCount = User::where('role', 'client')->count();
        $paiementsTotal = Paiement::sum('montant');
        
        // Récupération des statistiques globales pour l'affichage des KPIs de la réception
        $clientsCount = User::where('role', 'client')->count();
        $paiementsTotal = Paiement::sum('montant');
        
        // Calcul du nombre de clients ayant des factures non soldées
        $unpaidClientsCount = Depot::where('etat_paiement', '!=', 'payé')
                                   ->pluck('client_id')
                                   ->unique()
                                   ->count();

                                   
        // Identification des meilleurs clients basés sur le nombre de dépôts effectués
        $bestClients = User::where('role', 'client')
            ->withCount('depots as depots_count')
            ->orderByDesc('depots_count')
            ->take(5)
            ->get();

        $latestDepots = Depot::with(['client', 'linges.service'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        return view('receptionniste.dashboard', compact(
            'depotsCount', 
            'clientsCount', 
            'paiementsTotal', 
            'unpaidClientsCount',
            'bestClients',
            'latestDepots'
        ));
    }
}
