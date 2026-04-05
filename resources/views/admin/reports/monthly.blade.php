{{-- Nom : admin/reports/monthly.blade.php | Rôle : Analyse mensuelle des performances (Evolution temporelle) --}}
@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    {{-- En-tête avec sélection du mois et de l'année --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h2 class="fw-bold mb-0"><i class="fas fa-calendar-alt me-2 text-primary"></i>Rapport Mensuel</h2>
        <form action="{{ route('reports.monthly') }}" method="GET" class="d-flex gap-2 d-print-none flex-wrap">
            <select name="month" class="form-select bg-light border-0 fw-medium" style="min-width: 140px;">
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                        {{ ucfirst(\Carbon\Carbon::create()->month($m)->translatedFormat('F')) }}
                    </option>
                @endforeach
            </select>
            <select name="year" class="form-select bg-light border-0 fw-medium" style="min-width: 100px;">
                @foreach(range(date('Y')-2, date('Y')) as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">Filtrer</button>
        </form>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 p-4 h-100 position-relative overflow-hidden bg-white">
                <i class="bi bi-graph-up-arrow position-absolute opacity-10 text-primary" style="font-size: 6rem; top: -10px; right: -10px;"></i>
                <div class="small text-muted text-uppercase fw-bold mb-2">Total Dépôts (Valeur)</div>
                <div class="h2 mb-0 fw-bold text-dark">{{ number_format($totalDepots, 0, ',', ' ') }} <small class="fs-6">F</small></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 p-4 h-100 bg-success text-white position-relative overflow-hidden">
                <i class="bi bi-wallet2 position-absolute opacity-25" style="font-size: 6rem; top: -10px; right: -10px;"></i>
                <div class="small text-white text-uppercase fw-bold mb-2">Recettes Encaissées</div>
                <div class="h2 mb-0 fw-bold">{{ number_format($totalRevenu, 0, ',', ' ') }} <small class="fs-6">F</small></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 p-4 h-100 position-relative overflow-hidden bg-white">
                <i class="bi bi-calculator position-absolute opacity-10 text-info" style="font-size: 6rem; top: -10px; right: -10px;"></i>
                <div class="small text-muted text-uppercase fw-bold mb-2">Moyenne Journalière</div>
                <div class="h2 mb-0 fw-bold text-dark">{{ number_format($avgDaily, 0, ',', ' ') }} <small class="fs-6">F</small></div>
            </div>
        </div>
    </div>

    {{-- Tableau récapitulatif jour par jour du mois sélectionné --}}
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-calendar3 me-2 text-primary"></i>Détails par jour</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-nowrap">
                <thead class="bg-light">
                    <tr class="small text-muted text-uppercase fw-bold">
                        <th class="ps-4 border-0 py-3">Date</th>
                        <th class="border-0 py-3 text-center">Nbre Dépôts</th>
                        <th class="border-0 py-3 text-end">Valeur Dépôts</th>
                        <th class="pe-4 border-0 py-3 text-end">Revenus Encaissés</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dailyDetails as $dayInfo)
                    <tr class="border-bottom border-light">
                        <td class="ps-4 py-3 fw-medium text-dark">
                            <i class="bi bi-calendar-day me-2 text-muted"></i>{{ \Carbon\Carbon::parse($dayInfo['date'])->translatedFormat('d M Y') }}
                        </td>
                        <td class="py-3 text-center">
                            <span class="badge bg-light text-dark border px-2 py-1 fs-6">{{ $dayInfo['count_depots'] }}</span>
                        </td>
                        <td class="py-3 text-end fw-black text-primary fs-6">{{ number_format($dayInfo['val_depots'], 0, ',', ' ') }} F</td>
                        <td class="pe-4 py-3 text-end fw-black text-success fs-6">+{{ number_format($dayInfo['val_revenus'], 0, ',', ' ') }} F</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-3 text-secondary"></i>
                            <h6 class="fw-bold">Aucune donnée pour ce mois.</h6>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .fw-black { font-weight: 900; }
</style>
@endsection
