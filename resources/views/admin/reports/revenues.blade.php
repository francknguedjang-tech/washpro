{{-- Nom : admin/reports/revenues.blade.php | Rôle : Suivi détaillé des revenus réels encaissés sur une période --}}
@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <div class="card shadow-sm border-0 rounded-4 mb-4 bg-white">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h2 class="fw-bold mb-0"><i class="fas fa-hand-holding-usd me-2 text-primary"></i>Rapport des Revenus</h2>
                
                <form action="{{ route('reports.revenues') }}" method="GET" class="d-flex gap-2 d-print-none flex-wrap align-items-center">
                    <div class="input-group" style="width: auto;">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-calendar-event text-primary"></i></span>
                        <input type="date" name="start_date" class="form-control bg-light border-0 fw-medium" value="{{ $startDate }}" required>
                    </div>
                    <span class="text-muted fw-bold">au</span>
                    <div class="input-group" style="width: auto;">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-calendar-event text-primary"></i></span>
                        <input type="date" name="end_date" class="form-control bg-light border-0 fw-medium" value="{{ $endDate }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">Filtrer</button>
                    <button type="button" onclick="window.print()" class="btn btn-outline-secondary px-3 ms-md-2" title="Imprimer"><i class="bi bi-printer"></i></button>
                </form>
            </div>
        </div>
    </div>

    {{-- Bannière affichant le chiffre d'affaires total encaissé --}}
    <div class="card shadow-sm border-0 rounded-4 mb-4 bg-success text-white position-relative overflow-hidden">
        <i class="bi bi-graph-up-arrow position-absolute opacity-25" style="font-size: 8rem; top: -20px; right: -20px;"></i>
        <div class="card-body p-4 p-md-5 d-flex flex-column flex-md-row justify-content-between align-items-md-center position-relative z-1">
            <div>
                <h6 class="text-white text-uppercase fw-bold opacity-75 mb-2"><i class="bi bi-shield-check me-2"></i>Total des Revenus Encaissés</h6>
                <p class="mb-0 small opacity-75">Sur la période sélectionnée</p>
            </div>
            <div class="mt-3 mt-md-0">
                <h1 class="display-5 fw-bold mb-0">{{ number_format($grandTotal, 0, ',', ' ') }} <small class="fs-4">FCFA</small></h1>
            </div>
        </div>
    </div>

    {{-- Liste journalière des encaissements sur la période --}}
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-nowrap">
                <thead class="bg-light">
                    <tr class="small text-muted text-uppercase fw-bold">
                        <th class="ps-4 border-0 py-3">Date</th>
                        <th class="text-end pe-4 border-0 py-3">Montant Encaissé</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($revenues as $rev)
                    <tr class="border-bottom border-light">
                        <td class="ps-4 py-3 fw-medium text-dark">
                            <i class="bi bi-calendar-check me-2 text-muted"></i>{{ \Carbon\Carbon::parse($rev->date)->translatedFormat('l d F Y') }}
                        </td>
                        <td class="text-end pe-4 py-3 fw-black text-success fs-6">+{{ number_format($rev->total, 0, ',', ' ') }} F</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-3 text-secondary"></i>
                            <h6 class="fw-bold">Aucun revenu enregistré sur cette période.</h6>
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
    @media print {
        body { background-color: white !important; }
        nav, aside, .navbar, .d-print-none, form { display: none !important; }
        .card { box-shadow: none !important; border: 1px solid #ddd !important; }
        .card.bg-success { color: #000 !important; background-color: #f8fafc !important; border: 1px solid #000 !important;}
        .card.bg-success .text-white, .card.bg-success h6, .card.bg-success h1 { color: #000 !important; }
        @page { margin: 1cm; size: A4 portrait; }
    }
</style>
@endsection
