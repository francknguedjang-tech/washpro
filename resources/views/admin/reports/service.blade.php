{{-- Nom : admin/reports/service.blade.php | Rôle : Analyse de la rentabilité par type de service (Lavage, Repassage, etc.) --}}
@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <div class="card shadow-sm border-0 rounded-4 mb-4 bg-white">
        {{-- Filtre de période pour l'analyse des services --}}
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h2 class="fw-bold mb-0"><i class="fas fa-tags me-2 text-primary"></i>Rapport par Service</h2>
                <form action="{{ route('reports.service') }}" method="GET" class="d-flex gap-2 d-print-none flex-wrap align-items-center">
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
    {{-- Tableau comparatif des performances de chaque service --}}
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-nowrap">
                <thead class="bg-light">
                    <tr class="small text-muted text-uppercase fw-bold">
                        <th class="ps-4 border-0 py-3">Service</th>
                        <th class="border-0 py-3 text-center">Fois Utilisé (Commandes)</th>
                        <th class="border-0 py-3 text-center">Quantité Totale Traitée</th>
                        <th class="text-end pe-4 border-0 py-3">Chiffre d'Affaires</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $service)
                    <tr class="border-bottom border-light">
                        <td class="ps-4 py-3 fw-bold text-dark">
                            <i class="bi bi-tag text-muted me-2"></i>{{ $service->libelle }}
                        </td>
                        <td class="py-3 text-center">
                            <span class="badge bg-light text-dark border px-3 py-2 fs-6">{{ $service->count }}</span>
                        </td>
                        <td class="py-3 text-center fw-medium text-muted">
                            {{ number_format($service->total_quantity, 2) }}
                        </td>
                        <td class="text-end pe-4 py-3 text-primary fw-black fs-6">
                            {{ number_format($service->total_revenue, 0, ',', ' ') }} F
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-3 text-secondary"></i>
                            <h6 class="fw-bold">Aucun service utilisé sur cette période.</h6>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(count($services) > 0)
        <div class="card-footer bg-light border-top p-3 d-flex justify-content-between align-items-center">
            <span class="text-muted small fw-bold">Total Services Affichés : {{ count($services) }}</span>
            <h5 class="mb-0 text-dark fw-bold">CA Global : <span class="text-success ms-2">{{ number_format($services->sum('total_revenue'), 0, ',', ' ') }} F</span></h5>
        </div>
        @endif
    </div>
</div>

<style>
    .fw-black { font-weight: 900; }
    @media print {
        body { background-color: white !important; }
        nav, aside, .navbar, .d-print-none, form { display: none !important; }
        .card { box-shadow: none !important; border: 1px solid #ddd !important; margin-bottom: 20px;}
        @page { margin: 1cm; size: A4 portrait; }
    }
</style>
</div>
@endsection
