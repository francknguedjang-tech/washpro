{{-- Nom : receptionniste/dashboard.blade.php | Rôle : Tableau de bord de la Réception (Gestion des dépôts et nouveaux clients) --}}
@extends('layouts.receptionniste')

@section('content')
<div class="container-fluid px-4 py-4">
            <header class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h2 class="fw-bold mb-1">Espace Réception</h2>
                    <p class="text-muted mb-0">Vue d'ensemble de l'activité du pressing.</p>
                </div>
                <div class="d-flex gap-3">
                    {{-- Boutons d'action rapide pour la réception --}}
                    <a href="{{ route('clients.create') }}" class="btn btn-outline-primary fw-bold rounded-pill px-4">
                        <i class="bi bi-person-plus me-2"></i>Nouveau Client
                    </a>
                    <a href="{{ route('depots.create') }}" class="btn btn-primary fw-bold rounded-pill px-4 text-white">
                        <i class="bi bi-plus-lg me-2"></i>Nouveau Dépôt
                    </a>
                </div>
            </header>

            @if(session('success'))
                <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Ligne des Statistiques Rapides -->
            <div class="row g-4 mb-4">
                <!-- Card 1: Total Dépôts -->
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100 p-3" style="border-radius: 20px;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div class="p-3 bg-soft-primary text-primary rounded-4">
                                    <i class="bi bi-box-seam fs-4"></i>
                                </div>
                                <span class="badge bg-soft-primary text-primary rounded-pill px-3">Total</span>
                            </div>
                            <h3 class="fw-bold mb-1">{{ $depotsCount }}</h3>
                            <p class="text-muted small mb-0">Total dépôts</p>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Paiements Encaissés -->
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100 p-3" style="border-radius: 20px;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div class="p-3 bg-soft-success text-success rounded-4">
                                    <i class="bi bi-cash-stack fs-4"></i>
                                </div>
                                <span class="badge bg-soft-success text-success rounded-pill px-3">Recette</span>
                            </div>
                            <h3 class="fw-bold mb-1">{{ number_format($paiementsTotal, 0, ',', ' ') }} F</h3>
                            <p class="text-muted small mb-0">Paiements Encaissés</p>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Clients -->
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100 p-3" style="border-radius: 20px;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div class="p-3 bg-soft-info text-info rounded-4" style="background-color: rgba(13, 202, 240, 0.1);">
                                    <i class="bi bi-people fs-4"></i>
                                </div>
                                <span class="badge rounded-pill px-3" style="background-color: rgba(13, 202, 240, 0.1); color: #0dcaf0;">Inscrits</span>
                            </div>
                            <h3 class="fw-bold mb-1">{{ $clientsCount }}</h3>
                            <p class="text-muted small mb-0">Clients Inscrits</p>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Clients Impayés -->
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100 p-3" style="border-radius: 20px;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div class="p-3 bg-soft-danger text-danger rounded-4">
                                    <i class="bi bi-exclamation-triangle fs-4"></i>
                                </div>
                                <span class="badge bg-soft-danger text-danger rounded-pill px-3">Attention</span>
                            </div>
                            <h3 class="fw-bold mb-1">{{ $unpaidClientsCount }}</h3>
                            <p class="text-muted small mb-0">Clients avec Impayés</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <!-- Deposits Table -->
                <div class="col-lg-8">
                    <div class="table-card h-100">
                        <div class="p-4 bg-white border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0">Derniers Dépôts</h5>
                            <a href="{{ route('depots.index') }}" class="btn btn-link text-primary text-decoration-none fw-bold small">Tout voir</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr class="text-muted small border-bottom">
                                        <th class="ps-4 py-3">CODE</th>
                                        <th class="py-3">CLIENT</th>
                                        <th class="py-3">MONTANT</th>
                                        <th class="py-3">PAIEMENT</th>
                                        <th class="py-3">ÉTAT</th>
                                        <th class="pe-4 py-3 text-end">ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($latestDepots as $depot)
                                    <tr>
                                        <td class="ps-4 py-3 fw-bold">#{{ str_pad($depot->id, 5, '0', STR_PAD_LEFT) }}</td>
                                        <td class="py-3">
                                            <div class="fw-bold">{{ $depot->client->nom ?? 'Inconnu' }}</div>
                                            <div class="small text-muted">{{ $depot->client->telephone ?? '' }}</div>
                                        </td>
                                        <td class="py-3 fw-bold">{{ number_format($depot->prix_total, 0, ',', ' ') }} F</td>
                                        <td class="py-3">
                                            @php
                                                $payColor = match($depot->etat_paiement) {
                                                    'payé' => 'success',
                                                    'partiel' => 'warning',
                                                    default => 'danger'
                                                };
                                            @endphp
                                            <span class="badge bg-soft-{{ $payColor }} text-{{ $payColor == 'warning' ? 'dark' : $payColor }} rounded-pill px-3 border border-{{ $payColor }} border-opacity-25">
                                                {{ strtoupper($depot->etat_paiement) }}
                                            </span>
                                        </td>
                                        <td class="py-3">
                                            @php
                                                $stat_color = match($depot->etat) {
                                                    'pret' => 'success',
                                                    'recuperer' => 'primary',
                                                    default => 'warning'
                                                };
                                            @endphp
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-{{ $stat_color }} rounded-pill px-3 fw-bold dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    {{ ucfirst($depot->etat == 'recuperer' ? 'récupéré' : $depot->etat) }}
                                                </button>
                                                <ul class="dropdown-menu shadow border-0" style="border-radius: 12px; font-size: 0.85rem;">
                                                    <li><h6 class="dropdown-header text-uppercase small text-muted">Changer le statut</h6></li>
                                                    <li><a class="dropdown-item py-2 ajax-status-change" href="javascript:void(0)" data-url="{{ route('depots.updateStatus', $depot->id) }}" data-status="en cours"><span class="bg-warning rounded-circle d-inline-block me-2" style="width: 8px; height: 8px;"></span> En cours</a></li>
                                                    <li><a class="dropdown-item py-2 ajax-status-change" href="javascript:void(0)" data-url="{{ route('depots.updateStatus', $depot->id) }}" data-status="pret"><span class="bg-success rounded-circle d-inline-block me-2" style="width: 8px; height: 8px;"></span> Prêt</a></li>
                                                    <li><a class="dropdown-item py-2 ajax-status-change" href="javascript:void(0)" data-url="{{ route('depots.updateStatus', $depot->id) }}" data-status="recuperer"><span class="bg-primary rounded-circle d-inline-block me-2" style="width: 8px; height: 8px;"></span> Récupéré</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                        <td class="pe-4 py-3 text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-light btn-sm rounded-pill p-2 border shadow-sm" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" style="border-radius: 12px; min-width: 200px;">
                                                    <li><a href="{{ route('depots.show', $depot->id) }}" class="dropdown-item small py-2"><i class="bi bi-eye text-primary me-2"></i> Détails complets</a></li>
                                                    <li><a href="{{ route('depots.edit', $depot->id) }}" class="dropdown-item small py-2"><i class="bi bi-pencil shadow-sm text-warning me-2"></i> Modifier</a></li>
                                                    
                                                    <li><hr class="dropdown-divider"></li>
                                                    @if($depot->reste_a_payer > 0)
                                                    <li>
                                                        <a href="{{ route('depots.show', $depot->id) }}" class="dropdown-item small py-2 fw-bold text-success bg-soft-success">
                                                            <i class="bi bi-wallet2 me-2"></i> Payer le reste ({{ number_format($depot->reste_a_payer, 0, ',', ' ') }} F)
                                                        </a>
                                                    </li>
                                                    @else
                                                    <li><a href="{{ route('paiements.recu', $depot->id) }}" target="_blank" class="dropdown-item small py-2 text-success"><i class="bi bi-receipt me-2"></i> Imprimer le Reçu</a></li>
                                                    @endif
                                                    
                                                    @if(Auth::user()->role === 'admin')
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="{{ route('depots.destroy', $depot->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce dépôt ? Cette action est irréversible.');">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="dropdown-item small py-2 text-danger fw-bold"><i class="bi bi-trash me-2"></i> Supprimer</button>
                                                        </form>
                                                    </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            Aucun dépôt récent.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Liste des meilleurs clients (Fidélité) -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 24px;">
                        <div class="card-header bg-white border-bottom-0 p-4 pb-2">
                            <h5 class="fw-bold mb-0">Meilleurs Clients</h5>
                            <p class="text-muted small mb-0">Clients les plus fidèles</p>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <tbody>
                                        @forelse($bestClients as $client)
                                        <tr>
                                            <td class="ps-4 py-3 border-0 border-bottom border-light">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center me-3 fw-bold" style="width: 45px; height: 45px; font-size: 1.1rem; background-color: rgba(67, 97, 238, 0.1);">
                                                        {{ strtoupper(substr($client->nom, 0, 1) . substr($client->prenom, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 fw-bold text-dark">{{ $client->nom }} {{ $client->prenom }}</h6>
                                                        <small class="text-muted">{{ $client->telephone }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="pe-4 py-3 text-end border-0 border-bottom border-light">
                                                <span class="badge bg-light text-dark border rounded-pill px-3 py-2 fw-medium shadow-sm">
                                                    {{ $client->depots_count }} dépôts
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="2" class="text-center py-5 text-muted border-0">Aucun client.</td>
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
{{-- Script AJAX pour changer le statut du dépôt sans recharger la page --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusLinks = document.querySelectorAll('.ajax-status-change');
    
    statusLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.dataset.url;
            const newStatus = this.dataset.status;
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    _method: 'PATCH',
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    // Utilisation du Toast SweetAlert2 défini dans le layout
                    if (typeof Swal !== 'undefined' && Swal.mixin) {
                         const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        Toast.fire({
                            icon: 'success',
                            title: 'Statut mis à jour avec succès'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        window.location.reload();
                    }
                }
            });
        });
    });
});
</script>
@endsection
