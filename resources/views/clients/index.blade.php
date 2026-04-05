{{-- Nom : clients/index.blade.php | Rôle : Gestion et classement des clients (Fidélité et Impayés) --}}
@extends(Auth::user()->role === 'admin' ? 'layouts.admin' : 'layouts.receptionniste')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-800 text-dark mb-1">Gestion des Clients</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Tableau de bord</a></li>
                    <li class="breadcrumb-item active">Clients</li>
                </ol>
            </nav>
        </div>
        <button class="btn btn-primary rounded-pill px-4 fw-700 shadow-sm" data-bs-toggle="modal" data-bs-target="#newClientModal">
            <i class="bi bi-plus-lg me-2"></i>Nouveau Client
        </button>
    </div>

    {{-- Cartes de statistiques rapides sur la clientèle --}}
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card glass-panel border-0 rounded-4 shadow-sm h-100 p-2">
                <div class="card-body p-3 text-center">
                    <div class="d-inline-block p-2 bg-primary bg-opacity-10 text-primary rounded-circle mb-3" style="width: 48px; height: 48px; display: inline-flex !important; align-items: center; justify-content: center;">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                    <h4 class="fw-bold mb-1">{{ $totalClients }}</h4>
                    <p class="text-muted small mb-0 lh-tight">Total Clients</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card glass-panel border-0 rounded-4 shadow-sm h-100 p-2">
                <div class="card-body p-3 text-center">
                    <div class="d-inline-block p-2 bg-warning bg-opacity-10 text-warning rounded-circle mb-3" style="width: 48px; height: 48px; display: inline-flex !important; align-items: center; justify-content: center;">
                        <i class="bi bi-trophy-fill fs-4"></i>
                    </div>
                    <h4 class="fw-bold mb-1 text-truncate px-2">{{ $meilleurClient ? $meilleurClient->nom : 'N/A' }}</h4>
                    <p class="text-muted small mb-0 lh-tight">Meilleur Client</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card glass-panel border-0 rounded-4 shadow-sm h-100 p-2">
                <div class="card-body p-3 text-center">
                    <div class="d-inline-block p-2 bg-danger bg-opacity-10 text-danger rounded-circle mb-3" style="width: 48px; height: 48px; display: inline-flex !important; align-items: center; justify-content: center;">
                        <i class="bi bi-wallet2 fs-4"></i>
                    </div>
                    <h4 class="fw-bold mb-1">{{ $clientsAvecImpayes }}</h4>
                    <p class="text-muted small mb-0 lh-tight">Clients avec Impayés</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Liste principale des clients avec recherche et actions --}}
    <div class="card glass-panel border-0 rounded-4 shadow-sm">
        <div class="card-header bg-transparent border-0 p-4">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="fw-800 mb-0">Classement des Clients</h5>
                </div>
                <div class="col-auto">
                    <div class="input-group search-bar rounded-pill overflow-hidden border">
                        <span class="input-group-text bg-white border-0 ps-3">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-0 py-2 ps-2 pe-4" placeholder="Chercher un client...">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light bg-opacity-50">
                        <tr>
                            <th class="ps-4 py-3 text-uppercase small fw-700 text-muted">Rang</th>
                            <th class="py-3 text-uppercase small fw-700 text-muted">Client</th>
                            <th class="py-3 text-uppercase small fw-700 text-muted text-center">Dépôts</th>
                            <th class="py-3 text-uppercase small fw-700 text-muted text-end">Total Dépensé</th>
                            <th class="py-3 text-uppercase small fw-700 text-muted text-center">Statut</th>
                            <th class="pe-4 py-3 text-center text-uppercase small fw-700 text-muted">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $index => $client)
                        <tr>
                            <td class="ps-4">
                                <span class="badge {{ $index == 0 ? 'bg-warning text-dark' : 'bg-light text-muted' }} rounded-pill px-3 py-2 fw-700">
                                    #{{ $loop->iteration }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-primary bg-opacity-10 text-primary fw-700 me-3">
                                        {{ substr($client->nom, 0, 1) }}
                                    </div>
                                    <div>
                                        <h6 class="fw-700 mb-0">{{ $client->nom }} {{ $client->prenom }}</h6>
                                        <small class="text-muted">{{ $client->telephone }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center fw-600">{{ $client->depots_count }}</td>
                            <td class="text-end fw-800 text-primary">{{ number_format($client->total_depense, 0, ',', ' ') }} F</td>
                            <td class="text-center">
                                @if($client->a_impayes)
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Impayés</span>
                                @else
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">À jour</span>
                                @endif
                            </td>
                            <td class="pe-4 text-center">
                                <div class="dropdown">
                                    <button class="btn btn-icon btn-light rounded-circle p-0" type="button" data-bs-toggle="dropdown" style="width: 32px; height: 32px;">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3">
                                        <li><a class="dropdown-item py-2" href="{{ route('clients.show', $client->id) }}"><i class="bi bi-eye me-2"></i>Détails</a></li>
                                        <li><a class="dropdown-item py-2" href="{{ route('clients.edit', $client->id) }}"><i class="bi bi-pencil me-2"></i>Modifier</a></li>
                                        <li><a class="dropdown-item py-2" href="#" onclick="openNotifyModal({{ $client->id }}, '{{ addslashes($client->nom . ' ' . $client->prenom) }}')"><i class="bi bi-bell me-2 text-warning"></i>Relancer</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('clients.destroy', $client->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce client ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item py-2 text-danger">
                                                    <i class="bi bi-trash me-2"></i>Supprimer
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">Aucun client trouvé.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nouveau Client -->
<div class="modal fade" id="newClientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="fw-800 mb-0">Nouveau Client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('clients.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-700">PRÉNOM</label>
                        <input type="text" name="prenom" class="form-control rounded-3 py-2" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-700">NOM</label>
                        <input type="text" name="nom" class="form-control rounded-3 py-2" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-700">TÉLÉPHONE</label>
                        <input type="text" name="telephone" class="form-control rounded-3 py-2" placeholder="Ex: 0102030405" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-700">EMAIL (OPTIONNEL)</label>
                        <input type="email" name="email" class="form-control rounded-3 py-2" placeholder="client@exemple.com">
                    </div>
                    <div class="alert alert-info border-0 rounded-3 mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Un code d'accès sera généré automatiquement après la création.
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-700">Créer le client</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Relancer Client -->
<div class="modal fade" id="notifyClientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="fw-800 mb-0">Relancer le client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="notifyClientForm" action="" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-700">CLIENT VISÉ</label>
                        <input type="text" id="notifyClientName" class="form-control rounded-3 py-2 bg-light" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-700">MESSAGE DE RELANCE</label>
                        <textarea name="message" class="form-control rounded-3 py-2" rows="4" placeholder="Tapez votre message ici (ex: Vous avez un dépôt prêt ou des impayés...)" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-700">Envoyer la notification</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openNotifyModal(clientId, clientName) {
        const form = document.getElementById('notifyClientForm');
        // Setting the action URL dynamically based on the client ID
        form.action = `/admin/clients/${clientId}/notify`;
        
        const nameInput = document.getElementById('notifyClientName');
        nameInput.value = clientName;
        
        const modal = new bootstrap.Modal(document.getElementById('notifyClientModal'));
        modal.show();
    }
</script>

<style>
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .search-bar {
        min-width: 300px;
    }
    .glass-panel {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.6);
        box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08);
    }
</style>
@endsection
