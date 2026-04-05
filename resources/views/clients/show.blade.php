@extends(Auth::user()->role === 'admin' ? 'layouts.admin' : 'layouts.receptionniste')

@section('content')
<div class="container-fluid py-4">
    <!-- Header with Profile Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card glass-panel border-0 rounded-4 shadow-sm overflow-hidden">
                <div class="bg-primary bg-opacity-10 py-5 px-4 position-relative">
                    <div class="d-flex align-items-center">
                        <div class="avatar-xl bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-800 me-4 shadow-lg" style="width: 100px; height: 100px; font-size: 2.5rem; flex-shrink: 0; border: 4px solid white;">
                            {{ strtoupper(substr($client->nom, 0, 1) . substr($client->prenom, 0, 1)) }}
                        </div>
                        <div class="text-dark">
                            <h2 class="fw-800 mb-1 h1">{{ $client->prenom }} {{ $client->nom }}</h2>
                            <div class="d-flex flex-wrap gap-3">
                                <span class="badge bg-white text-primary border rounded-pill px-3 py-2 fw-700 shadow-sm">
                                    <i class="bi bi-telephone-fill me-2"></i>{{ $client->telephone }}
                                </span>
                                @if($client->email)
                                <span class="badge bg-white text-muted border rounded-pill px-3 py-2 fw-700 shadow-sm">
                                    <i class="bi bi-envelope-fill me-2"></i>{{ $client->email }}
                                </span>
                                @endif
                                <span class="badge bg-white text-muted border rounded-pill px-3 py-2 fw-700 shadow-sm">
                                    <i class="bi bi-calendar3 me-2"></i>Client depuis le {{ $client->created_at->format('d/m/Y') }}
                                </span>
                            </div>
                        </div>
                        <div class="ms-auto d-none d-md-block">
                            <button class="btn btn-primary rounded-pill px-4 fw-700 shadow-sm" data-bs-toggle="modal" data-bs-target="#notifClientModal">
                                <i class="bi bi-bell-fill me-2"></i>Notifier le Client
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4 border-top">
                    <div class="row g-4 text-center">
                        <div class="col-md-3">
                            <h4 class="fw-800 text-primary mb-1">{{ $stats['total_depots'] }}</h4>
                            <p class="text-muted small fw-700 mb-0 text-uppercase">Dépôts Totaux</p>
                        </div>
                        <div class="col-md-3 border-start border-light">
                            <h4 class="fw-800 text-success mb-1">{{ number_format($stats['total_depense'], 0, ',', ' ') }} F</h4>
                            <p class="text-muted small fw-700 mb-0 text-uppercase">Chiffre d'Affaires</p>
                        </div>
                        <div class="col-md-3 border-start border-light">
                            <h4 class="fw-800 text-warning mb-1">{{ $stats['en_cours'] }}</h4>
                            <p class="text-muted small fw-700 mb-0 text-uppercase">En Cours</p>
                        </div>
                        <div class="col-md-3 border-start border-light">
                            <h4 class="fw-800 text-danger mb-1">{{ $stats['a_impayes'] }}</h4>
                            <p class="text-muted small fw-700 mb-0 text-uppercase">Impayés/Non Terminés</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- History Tables -->
    <div class="row">
        <div class="col-12">
            <div class="card glass-panel border-0 rounded-4 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-800 mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Historique des Dépôts</h5>
                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-700">
                            <i class="bi bi-download me-2"></i>Exporter PDF
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light bg-opacity-50">
                                <tr>
                                    <th class="ps-4 py-3 text-uppercase small fw-700 text-muted">Référence</th>
                                    <th class="py-3 text-uppercase small fw-700 text-muted">Date Dépôt</th>
                                    <th class="py-3 text-uppercase small fw-700 text-muted">Articles</th>
                                    <th class="py-3 text-uppercase small fw-700 text-muted text-end">Montant Total</th>
                                    <th class="py-3 text-uppercase small fw-700 text-muted text-center">Statut</th>
                                    <th class="pe-4 py-3 text-end text-uppercase small fw-700 text-muted">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($client->depots as $depot)
                                <tr>
                                    <td class="ps-4">
                                        <span class="fw-800 text-dark">{{ $depot->reference }}</span>
                                    </td>
                                    <td>{{ $depot->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($depot->linges as $linge)
                                                <span class="badge bg-light text-muted fw-600 rounded-pill border">{{ $linge->quantite }}x {{ $linge->description }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="text-end fw-800 text-primary">{{ number_format($depot->prix_total, 0, ',', ' ') }} F</td>
                                    <td class="text-center">
                                        @php
                                            $badgeClass = match($depot->etat) {
                                                'en cours' => 'bg-warning',
                                                'pret' => 'bg-info',
                                                'recuperer' => 'bg-success',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }} bg-opacity-10 text-{{ str_replace('bg-', '', $badgeClass) }} rounded-pill px-3 py-2 fw-700">
                                            {{ ucfirst($depot->etat) }}
                                        </span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <a href="{{ route('depots.show', $depot->id) }}" class="btn btn-sm btn-icon btn-light rounded-circle shadow-sm">
                                            <i class="bi bi-eye text-primary"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">Auncun dépôt enregistré pour ce client.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .avatar-xl {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .avatar-md {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .btn-icon {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
    .glass-panel {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.6);
        box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08);
    }
    .fw-800 { font-weight: 800; }
    .fw-700 { font-weight: 700; }
    .fw-600 { font-weight: 600; }
</style>
@endsection

<!-- Modal Notification Client -->
<div class="modal fade" id="notifClientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="fw-800 mb-0">Envoyer une Notification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('clients.notify', $client->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-700">MESSAGE</label>
                        <textarea name="message" class="form-control rounded-3 py-2 px-3 bg-light border-0" rows="4" placeholder="Tapez votre message ici..." required></textarea>
                    </div>
                    <div class="alert alert-info border-0 rounded-3 mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Le message sera envoyé à {{ $client->prenom }} via le système de notifications interne.
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-700">Envoyer</button>
                </div>
            </form>
        </div>
    </div>
</div>
