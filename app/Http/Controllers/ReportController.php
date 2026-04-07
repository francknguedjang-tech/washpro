<?php

namespace App\Http\Controllers;

use App\Models\Depot;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Affiche la page principale de sélection des rapports pour l'administrateur.
     */
    public function index()
    {
        return view('admin.reports.index');
    }

    /**
     * Génère et affiche le rapport journalier (Recettes et Dépôts d'aujourd'hui).
     */
    public function daily()
    {
        $today = Carbon::today();
        $depots = Depot::whereDate('date_depot', $today)->with(['client', 'linges.service'])->get();
        $paiements = Paiement::whereDate('date_paiement', $today)->with('depot.client')->get();

        $totalDepots = $depots->sum('prix_total');
        $totalRevenu = $paiements->sum('montant');

        return view('admin.reports.daily', compact('depots', 'paiements', 'totalDepots', 'totalRevenu'));
    }

    /**
     * Génère et affiche le rapport mensuel détaillé jour par jour.
     */
    public function monthly(Request $request)
    {
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);

        $depots = Depot::whereMonth('date_depot', $month)
            ->whereYear('date_depot', $year)
            ->get();

        $paiements = Paiement::whereMonth('date_paiement', $month)
            ->whereYear('date_paiement', $year)
            ->get();

        $totalDepots = $depots->sum('prix_total');
        $totalRevenu = $paiements->sum('montant');
        
        $daysInMonth = Carbon::create($year, $month)->daysInMonth;
        $avgDaily = $totalRevenu / ($daysInMonth ?: 1);

        // Grouping for daily details
        $dailyDetails = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $d);
            
            $dailyDepots = $depots->filter(function($item) use ($dateStr) {
                return Carbon::parse($item->date_depot)->format('Y-m-d') == $dateStr;
            });

            $dailyPaiements = $paiements->filter(function($item) use ($dateStr) {
                return Carbon::parse($item->date_paiement)->format('Y-m-d') == $dateStr;
            });

            $valDepots = $dailyDepots->sum('prix_total');
            $valRevenus = $dailyPaiements->sum('montant');

            if ($dailyDepots->count() > 0 || $valRevenus > 0) {
                $dailyDetails[] = [
                    'day' => $d,
                    'date' => $dateStr,
                    'count_depots' => $dailyDepots->count(),
                    'val_depots' => $valDepots,
                    'val_revenus' => $valRevenus
                ];
            }
        }
        
        // Reverse array to show newest days first
        $dailyDetails = array_reverse($dailyDetails);

        return view('admin.reports.monthly', compact('totalDepots', 'totalRevenu', 'avgDaily', 'month', 'year', 'dailyDetails'));
    }

    /**
     * Génère un rapport détaillant les performances par type de service (Lavage, Repassage...) 
     * sur une période donnée.
     */
    public function byService(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $services = DB::table('services')
            ->leftJoin('linges', 'services.id', '=', 'linges.service_id')
            ->leftJoin('depots', 'linges.depot_id', '=', 'depots.id')
            ->whereBetween('depots.date_depot', [$startDate, $endDate])
            ->select(
                'services.libelle',
                DB::raw('count(linges.id) as count'),
                DB::raw('sum(linges.quantite) as total_quantity'),
                DB::raw('sum(linges.quantite * linges.prix_unitaire) as total_revenue') // Simplified total
            )
            ->groupBy('services.id', 'services.libelle')
            ->get();

        return view('admin.reports.service', compact('services', 'startDate', 'endDate'));
    }

    /**
     * Génère un rapport ciblé exclusivement sur l'évolution globale des revenus financiers.
     */
    public function revenues(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $revenues = Paiement::whereBetween('date_paiement', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(date_paiement) as date'),
                DB::raw('SUM(montant) as total')
            )
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->get();

        $grandTotal = $revenues->sum('total');

        return view('admin.reports.revenues', compact('revenues', 'grandTotal', 'startDate', 'endDate'));
    }
}
