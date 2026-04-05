{{-- Nom : paiements/index.blade.php | Rôle : Journal de tous les paiements effectués par les clients --}}
@extends(Auth::user()->role === 'admin' ? 'layouts.admin' : 'layouts.receptionniste')

@section('content')
<div class="container-fluid px-3 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1 h3">Historique des Paiements</h2>
            <p class="text-muted mb-0 small">Consultez et filtrez tous les paiements enregistrés</p>
        </div>
    </div>

    {{-- Barre de recherche et filtres de paiement --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-4">
            <form action="{{ route('paiements.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-12 col-md-5">
                    <label class="form-label small fw-bold text-muted text-uppercase mb-2">Recherche Rapide</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search text-primary"></i></span>
                        <input type="text" name="q" class="form-control bg-light border-0 py-2" placeholder="Nom, Tel ou Réf Dépôt..." value="{{ request('q') }}">
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-bold text-muted text-uppercase mb-2">Date du Paiement</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-calendar-event text-primary"></i></span>
                        <input type="date" name="date" class="form-control bg-light border-0 py-2" value="{{ request('date') }}">
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                    <label class="form-label small fw-bold text-muted text-uppercase mb-2">Mode de Paiement</label>
                    <div class="d-flex gap-2">
                        <select name="mode" class="form-select bg-light border-0 py-2">
                            <option value="">Tous les modes</option>
                            <option value="cache" {{ request('mode') == 'cache' ? 'selected' : '' }}>Espèces</option>
                            <option value="orange_money" {{ request('mode') == 'orange_money' ? 'selected' : '' }}>Orange Money</option>
                            <option value="mobile_money" {{ request('mode') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                        </select>
                        <button type="submit" class="btn btn-primary px-3 rounded-3" data-bs-toggle="tooltip" title="Filtrer"><i class="bi bi-funnel-fill"></i></button>
                        <a href="{{ route('paiements.index') }}" class="btn btn-light px-3 rounded-3" data-bs-toggle="tooltip" title="Réinitialiser"><i class="bi bi-arrow-counterclockwise"></i></a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 h-100">
        <div class="card-body p-0">
            {{-- Liste chronologique des entrées d'argent --}}
            <div class="table-responsive">
                <table class="table align-middle mb-0 table-hover text-nowrap">
                    <thead class="bg-light sticky-top">
                        <tr class="small text-muted text-uppercase fw-bold">
                            <th class="ps-4 border-0 py-3">Réf Dépôt</th>
                            <th class="border-0 py-3">Client</th>
                            <th class="border-0 py-3 text-center">Date</th>
                            <th class="border-0 py-3 text-center">Mode</th>
                            <th class="border-0 py-3 text-end">Montant Payé</th>
                            <th class="border-0 py-3 text-end">Reste à Payer</th>
                            <th class="pe-4 border-0 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paiements as $paiement)
                        <tr class="border-bottom border-light">
                            <td class="ps-4 py-3 fw-bold text-primary">
                                <a href="{{ route('depots.show', $paiement->depot_id) }}" class="text-decoration-none">
                                    {{ $paiement->depot->reference ?? 'DP-'.$paiement->depot_id }}
                                </a>
                            </td>
                            <td class="py-3">
                                <div class="fw-bold text-dark">{{ $paiement->depot->client->prenom ?? '' }} {{ $paiement->depot->client->nom ?? '' }}</div>
                                <div class="small text-muted">{{ $paiement->depot->client->telephone ?? '' }}</div>
                            </td>
                            <td class="py-3 text-center fw-medium text-muted">{{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') }}</td>
                            <td class="py-3 text-center">
                                @php
                                    $modeBagdes = [
                                        'cache' => 'bg-secondary',
                                        'orange_money' => 'bg-orange',
                                        'mobile_money' => 'bg-yellow'
                                    ];
                                    $badge = $modeBagdes[$paiement->mode_paiement] ?? 'bg-secondary';
                                @endphp
                                <span class="badge {{ $badge }} text-uppercase px-2 py-1 rounded-pill">{{ str_replace('_', ' ', $paiement->mode_paiement) }}</span>
                            </td>
                            <td class="py-3 text-end fw-black text-success fs-6">
                                +{{ number_format($paiement->montant, 0, ',', ' ') }} F
                            </td>
                            <td class="py-3 text-end fw-bold text-warning">
                                {{ number_format($paiement->depot?->reste_a_payer ?? 0, 0, ',', ' ') }} F
                            </td>
                            <td class="pe-4 py-3 text-center">
                                <div class="btn-group shadow-sm">
                                    <a href="{{ route('depots.show', $paiement->depot_id) }}" class="btn btn-sm btn-light border text-primary" data-bs-toggle="tooltip" title="Détails du dépôt">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <a href="{{ route('paiements.recu', $paiement->depot_id) }}" target="_blank" class="btn btn-sm btn-light border text-success" data-bs-toggle="tooltip" title="Imprimer Reçu">
                                        <i class="bi bi-printer-fill"></i>
                                    </a>
                                    <form action="{{ route('paiements.destroy', $paiement->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce paiement ? Cette action est irréversible et recalculera le solde du dépôt.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger" data-bs-toggle="tooltip" title="Supprimer">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                Aucun paiement trouvé pour ces critères.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($paiements->count() > 0)
            <div class="p-3 bg-light border-top mt-auto d-flex justify-content-between align-items-center rounded-bottom-4">
                <span class="text-muted small fw-medium">
                    <i class="bi bi-info-circle me-1"></i> {{ $paiements->count() }} paiement(s) affiché(s)
                </span>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    .bg-orange { background-color: #ff7900; color: white; }
    .bg-yellow { background-color: #fbd600; color: #333; }
    .table th { font-weight: 700; font-size: 0.75rem; letter-spacing: 0.05em; border: none; }
    .fw-black { font-weight: 900; }
    .btn-group .btn { transition: all 0.2s; }
    .btn-group .btn:hover { background-color: #f8f9fa; transform: translateY(-1px); }
</style>
@endsection
