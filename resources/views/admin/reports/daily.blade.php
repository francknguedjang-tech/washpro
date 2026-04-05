{{-- Nom : admin/reports/daily.blade.php | Rôle : Détail des statistiques et dépôts du jour actuel --}}
@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="fas fa-calendar-day me-2 text-primary"></i>Rapport Journalier</h2>
        <button onclick="window.print()" class="btn btn-outline-primary d-print-none">
            <i class="fas fa-print me-2"></i>Imprimer
        </button>
    </div>

    {{-- Cartes de résumé financier du jour --}}
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 bg-primary text-white p-4 h-100 position-relative overflow-hidden">
                <i class="bi bi-box-seam position-absolute opacity-25" style="font-size: 5rem; top: -10px; right: -10px;"></i>
                <div class="small opacity-75 text-uppercase fw-bold mb-2">Valeur Totale des Dépôts</div>
                <div class="h2 mb-0 fw-bold">{{ number_format($totalDepots, 0, ',', ' ') }} <small class="fs-5">FCFA</small></div>
                <div class="mt-3 small opacity-75"><i class="bi bi-info-circle me-1"></i>Chiffre généré aujourd'hui</div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 bg-success text-white p-4 h-100 position-relative overflow-hidden">
                <i class="bi bi-cash-stack position-absolute opacity-25" style="font-size: 5rem; top: -10px; right: -10px;"></i>
                <div class="small opacity-75 text-uppercase fw-bold mb-2">Recettes Encaissées</div>
                <div class="h2 mb-0 fw-bold">{{ number_format($totalRevenu, 0, ',', ' ') }} <small class="fs-5">FCFA</small></div>
                <div class="mt-3 small opacity-75"><i class="bi bi-info-circle me-1"></i>Argent réel perçu</div>
            </div>
        </div>
        <div class="col-md-12 col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 bg-white p-4 h-100 d-flex justify-content-center align-items-center border-start border-5 border-info">
                <div class="text-center">
                    <div class="text-muted text-uppercase fw-bold small mb-2">Nombre de dépôts</div>
                    <div class="display-5 fw-black text-dark">{{ $depots->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tableau listant chaque dépôt avec ses articles --}}
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-list-check me-2 text-primary"></i>Liste des Dépôts du jour</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-nowrap">
                <thead class="bg-light">
                    <tr class="small text-muted text-uppercase fw-bold">
                        <th class="ps-4 border-0 py-3">Client</th>
                        <th class="border-0 py-3">Articles & Services</th>
                        <th class="border-0 py-3 text-end">Prix Total</th>
                        <th class="border-0 py-3 text-center pe-4">Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($depots as $depot)
                    <tr class="border-bottom border-light">
                        <td class="ps-4 py-3">
                            <div class="fw-bold text-dark">{{ $depot->client->nom ?? '' }} {{ $depot->client->prenom ?? '' }}</div>
                            <div class="small text-muted"><i class="bi bi-telephone me-1"></i>{{ $depot->client->telephone ?? 'N/A' }}</div>
                        </td>
                        <td class="py-3">
                            <div style="max-width: 300px; white-space: normal;">
                                @foreach($depot->linges as $linge)
                                <div class="badge bg-light text-dark border me-1 mb-1 fw-medium px-2 py-1">
                                    {{ $linge->quantite }}{{ $linge->service->unite == 'kg' ? 'kg' : 'x' }} {{ $linge->service->libelle }}
                                </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="py-3 text-end fw-black text-primary fs-6">{{ number_format($depot->prix_total, 0, ',', ' ') }} F</td>
                        <td class="py-3 text-center pe-4">
                            @php
                                $badgeClass = match($depot->etat) {
                                    'en cours' => 'warning',
                                    'pret' => 'success',
                                    'recuperer' => 'primary',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $badgeClass }} px-3 py-2 rounded-pill text-uppercase">{{ $depot->etat }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-3 text-secondary"></i>
                            <h6 class="fw-bold">Aucun dépôt enregistré aujourd'hui.</h6>
                            <p class="small mb-0">Les dépôts créés s'afficheront ici.</p>
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
    .border-info { border-color: #0dcaf0 !important; }
    @media print {
        body { background-color: white !important; }
        nav, aside, .navbar, .d-print-none, form { display: none !important; }
        .card { box-shadow: none !important; border: 1px solid #ddd !important; }
        .card.bg-primary, .card.bg-success { color: #000 !important; background-color: #f8fafc !important; border: 1px solid #000 !important;}
        .card.bg-primary .text-white, .card.bg-success .text-white { color: #000 !important; }
        .card-header { background-color: white !important; }
        @page { margin: 1cm; size: A4 portrait; }
    }
</style>
@endsection
