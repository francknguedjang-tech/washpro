<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\depot;
use App\Models\paiement;
use Carbon\Carbon;

class ClientDashboardController extends Controller
{
    /**
     * Constructeur pour sécuriser l'accès au rôle "client" uniquement.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:client']);
    }

    /**
     * Affiche le tableau de bord du client.
     * Charge l'historique de ses dépôts, ses statistiques mensuelles et ses paiements.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        

        // Récupération de l'historique complet des dépôts du client
        $depots = Depot::where('client_id', $user->id)
            ->with(['linges.service', 'paiements'])
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total_depots' => $depots->count(),
            'en_cours' => $depots->whereIn('etat', ['en attente', 'en cours', 'en traitement'])->count(),
            'pret' => $depots->where('etat', 'pret')->count(),
            'total_depense' => $depots->sum('prix_total'),
        ];

        // Préparation des données du graphique (Dépôts par mois sur les 6 derniers mois)
        Carbon::setLocale('fr');
        $chartData = ['labels' => [], 'data' => []];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $count = Depot::where('client_id', $user->id)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $chartData['labels'][] = ucfirst($month->translatedFormat('M Y'));
            $chartData['data'][] = $count;
        }

        // Récupération sécurisée de l'historique des paiements
        $paiements = Paiement::whereHas('depot', function($query) use ($user) {
            $query->where('client_id', $user->id);
        })->with('depot')->orderBy('date_paiement', 'desc')->get();

        // Récupération des dernières notifications non lues
        $notifications = \App\Models\Notification::where('user_id', $user->id)
                            ->where('lu', false)
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();

        return view('client.dashboard', compact('depots', 'stats', 'chartData', 'paiements', 'notifications'));
    }

    /**
     * Affiche le reçu financier d'un dépôt spécifique.
     * Limité par sécurité aux dépôts appartenant au client connecté.
     */
    public function genererRecu($id)
    {
        $depot = Depot::with(['client', 'paiements', 'service', 'receptionniste'])
                      ->where('client_id', Auth::id())
                      ->findOrFail($id);
                      
        return view('paiements.recu', compact('depot'));
    }
}
