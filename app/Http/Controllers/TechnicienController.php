<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\depot;

class TechnicienController extends Controller
{
    /**
     * Constructeur pour sécuriser l'accès de ce contrôleur
     * uniquement aux utilisateurs avec le rôle "technicien".
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:technicien']);
    }

    /**
     * Affiche le tableau de bord du technicien avec sa file d'attente (dépôts en cours)
     * et les statistiques de production récentes.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function dashboard()
    {
        // Récupère la liste de tous les dépôts qui nécessitent une intervention (statut: en cours)
        $depotsEnAttente = Depot::with(['linges.service'])
            ->where('etat', 'en cours')
            ->orderBy('date_depot', 'asc') 
            ->get();
            
        $recentCompleted = Depot::with(['linges.service'])
            ->whereIn('etat', ['pret', 'recuperer'])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        $stats = [
            'en_attente' => Depot::where('etat', 'en cours')->count(),
            'prets_aujourdhui' => Depot::where('etat', 'pret')->whereDate('updated_at', today())->count(),
            'termines' => Depot::where('etat', 'recuperer')->count(),
            'poids_aujourdhui' => Depot::with('linges.service')
                                        ->whereIn('etat', ['pret', 'recuperer'])
                                        ->whereDate('updated_at', today())
                                        ->get()
                                        ->sum('total_poids'),
        ];

        return view('technicien.dashboard', compact('depotsEnAttente', 'recentCompleted', 'stats'));
    }
}
