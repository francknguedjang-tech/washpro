{{-- Nom : dashboard.blade.php | Rôle : Tableau de bord principal de l'Administrateur (Statistiques et Graphiques) --}}
@extends('layouts.admin')

@section('content')
<div class="container-fluid px-3 py-3">
    <!-- Header Section -->
    <div class="mb-5">
        <h2 class="fw-bold text-dark mb-1">Tableau de bord</h2>
        <p class="text-muted">Bienvenue, voici un aperçu de votre activité.</p>
    </div>

    <!-- Section des Cartes de Statistiques (6 Indicateurs Clés) -->
    <div class="row g-4 mb-4">
        <!-- 1: Revenus Total -->
        <div class="col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm h-100 p-2" style="border-radius: 16px;">
                <div class="card-body text-center p-3">
                    <div class="d-inline-block p-2 bg-soft-success text-success rounded-circle mb-3">
                        <i class="bi bi-cash-stack fs-4"></i>
                    </div>
                    <h5 class="fw-bold mb-1" style="font-size: 1.1rem;">{{ number_format($revenusTotal, 0, ',', ' ') }} F</h5>
                    <p class="text-muted small mb-0 lh-tight">Paiements Encaissés</p>
                </div>
            </div>
        </div>

        <!-- 2: Total Impayés -->
        <div class="col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm h-100 p-2" style="border-radius: 16px;">
                <div class="card-body text-center p-3">
                    <div class="d-inline-block p-2 bg-soft-danger text-danger rounded-circle mb-3">
                        <i class="bi bi-wallet2 fs-4"></i>
                    </div>
                    <h5 class="fw-bold mb-1" style="font-size: 1.1rem;">{{ number_format($totalNonPaye, 0, ',', ' ') }} F</h5>
                    <p class="text-muted small mb-0 lh-tight">non payé</p>
                </div>
            </div>
        </div>

        <!-- 3: Total Clients Inscrits -->
        <div class="col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm h-100 p-2" style="border-radius: 16px;">
                <div class="card-body text-center p-3">
                    <div class="d-inline-block p-2 bg-soft-primary text-primary rounded-circle mb-3">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                    <h5 class="fw-bold mb-1" style="font-size: 1.1rem;">{{ $totalClients }}</h5>
                    <p class="text-muted small mb-0 lh-tight">Clients Inscrits</p>
                </div>
            </div>
        </div>

        <!-- 4: Clients Actifs -->
        <div class="col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm h-100 p-2" style="border-radius: 16px;">
                <div class="card-body text-center p-3">
                    <div class="d-inline-block p-2 bg-soft-info text-info rounded-circle mb-3">
                        <i class="bi bi-person-check-fill fs-4"></i>
                    </div>
                    <h5 class="fw-bold mb-1" style="font-size: 1.1rem;">{{ $clientsActifs }}</h5>
                    <p class="text-muted small mb-0 lh-tight">Clients Actifs</p>
                </div>
            </div>
        </div>

        <!-- 5: Total Dépôts -->
        <div class="col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm h-100 p-2" style="border-radius: 16px;">
                <div class="card-body text-center p-3">
                    <div class="d-inline-block p-2 bg-soft-primary text-primary rounded-circle mb-3" style="background-color: rgba(67, 97, 238, 0.1);">
                        <i class="bi bi-box-seam-fill fs-4"></i>
                    </div>
                    <h5 class="fw-bold mb-1" style="font-size: 1.1rem;">{{ $depotsCount }}</h5>
                    <p class="text-muted small mb-0 lh-tight"> Dépôts Enregistrés</p>
                </div>
            </div>
        </div>

        <!-- 6: Prêt mais non retiré -->
        <div class="col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm h-100 p-2" style="border-radius: 16px;">
                <div class="card-body text-center p-3">
                    <div class="d-inline-block p-2 bg-soft-success text-success rounded-circle mb-3">
                        <i class="bi bi-check-circle-fill fs-4"></i>
                    </div>
                    <h5 class="fw-bold mb-1" style="font-size: 1.1rem;">{{ $pretCount }}</h5>
                    <p class="text-muted small mb-0 lh-tight">Dépôts Prêts</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Section des Graphiques (Analyse visuelle) -->
    <div class="row g-4 mb-5">
        <!-- Chiffre d'affaire Line Chart -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 20px;">
                <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-graph-up-arrow text-primary me-2"></i>Évolution Financière Mensuelle</h5>
                <div style="height: 300px; width: 100%;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
        <!-- Enregistrements Bar Chart -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 20px;">
                <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-bar-chart-fill text-success me-2"></i>Dépôts Enregistrés par Mois</h5>
                <div style="height: 300px; width: 100%;">
                    <canvas id="depositsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Grille de la section principale : Liste et Actions -->
    <div class="row g-4">
        <!-- Latest Deposits Table -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 24px;">
                <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Derniers Dépôts</h5>
                    {{-- Lien vers la liste complète des dépôts --}}
                    <a href="{{ route('depots.index') }}" class="btn btn-link text-primary text-decoration-none fw-bold small">Tout voir</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr class="text-muted small border-bottom">
                                    <th class="ps-4 py-3">CODE</th>
                                    <th class="py-3">CLIENT</th>
                                    <th class="py-3">MONTANT</th>
                                    <th class="py-3">PAIEMENT</th>
                                    <th class="py-3">ÉTAT</th>
                                    {{-- Colonne pour les actions rapides --}}
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
                                        {{-- Logique d'affichage de la couleur du badge de paiement --}}
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
                                        {{-- Logique d'affichage de la couleur selon l'état du linge --}}
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
                                            {{-- Menu déroulant pour changer l'état du dépôt sans recharger la page (AJAX) --}}
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
                                                
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('depots.destroy', $depot->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce dépôt ? Cette action est irréversible.');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="dropdown-item small py-2 text-danger fw-bold"><i class="bi bi-trash me-2"></i> Supprimer</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        Aucun dépôt récent.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Action Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 text-center p-5 h-100 position-relative overflow-hidden" style="border-radius: 24px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white; box-shadow: 0 15px 30px rgba(67, 97, 238, 0.25);">
                <div class="position-absolute" style="top: -20px; right: -20px; opacity: 0.1; transform: rotate(15deg);">
                    <i class="bi bi-asterisk" style="font-size: 15rem;"></i>
                </div>
                <div class="mb-4 position-relative z-1">
                    <div class="display-3 text-white"><i class="bi bi-lightning-charge-fill"></i></div>
                </div>
                <h4 class="fw-bold mb-3 position-relative z-1 text-white">Action Rapide</h4>
                <p class="text-white opacity-75 mb-5 position-relative z-1">Créez un nouveau dépôt client en quelques secondes.</p>
                <a href="{{ route('depots.create') }}" class="btn btn-light btn-lg text-primary shadow-sm w-100 py-3 rounded-pill fw-bold position-relative z-1 hover-lift">
                    <i class="bi bi-plus-lg me-2"></i>Nouveau Dépôt
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-soft-primary { background-color: rgba(67, 97, 238, 0.1); }
    .bg-soft-success { background-color: rgba(46, 204, 113, 0.1); }
    .bg-soft-warning { background-color: rgba(243, 156, 18, 0.1); }
    .bg-soft-danger { background-color: rgba(231, 76, 60, 0.1); }
    
    .text-primary { color: var(--primary-color) !important; }
    .text-success { color: var(--success-color) !important; }
    .text-warning { color: var(--warning-color) !important; }
    .text-danger { color: var(--danger-color) !important; }

    .card { 
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
        box-shadow: 0 10px 20px rgba(0,0,0,0.02) !important;
    }
    .card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.06) !important; }

    .table th { font-weight: 700; font-size: 0.8rem; letter-spacing: 0.05em; color: #94a3b8; }
    .table td { vertical-align: middle; border-bottom-color: #f1f5f9; }
    .table-hover tbody tr:hover { background-color: #f8fafc; }
    
    .badge { font-weight: 700; font-size: 0.75rem; letter-spacing: 0.3px; padding: 0.4em 0.8em; }
    
    .hover-lift { transition: transform 0.2s; }
    .hover-lift:hover { transform: translateY(-3px); }
    
    .opacity-50 { opacity: 0.5; }
    .z-1 { z-index: 1; }
</style>

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
                    if (typeof Swal !== 'undefined' && Swal.mixin) {
                        const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
                        Toast.fire({ icon: 'success', title: data.message || 'Statut mis à jour !' });
                    }

                    // Update button UI directly
                    const btn = this.closest('.dropdown').querySelector('.dropdown-toggle');
                    btn.innerHTML = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                    
                    // Update classes for color
                    btn.className = btn.className.replace(/btn-(warning|success|primary)/, '');
                    if(newStatus === 'en cours') btn.classList.add('btn-warning');
                    else if(newStatus === 'pret') btn.classList.add('btn-success');
                    else if(newStatus === 'recuperer') btn.classList.add('btn-primary');
                }
            })
            .catch(error => {
                console.error("Error updating status:", error);
            });
        });
    });
});
</script>

<script src="{{ asset('vendor/chartjs/chart.umd.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];
    
    // Revenue Line Chart
    const ctxRevenus = document.getElementById('revenueChart').getContext('2d');
    const revenusData = @json($chartRevenus);
    new Chart(ctxRevenus, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Chiffre d\'Affaires (F)',
                data: revenusData,
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#2563eb',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return new Intl.NumberFormat('fr-FR').format(context.raw) + ' F';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { borderDash: [4, 4], color: '#e2e8f0' },
                    ticks: {
                        callback: function(value) {
                            if(value >= 1000) return (value/1000) + 'k';
                            return value;
                        }
                    }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // Deposits Bar Chart
    const ctxDepots = document.getElementById('depositsChart').getContext('2d');
    const depotsData = @json($chartDepots);
    new Chart(ctxDepots, {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Dépôts enregistrés',
                data: depotsData,
                backgroundColor: '#10b981', // green / success color
                borderRadius: 6,
                barPercentage: 0.6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { borderDash: [4, 4], color: '#e2e8f0' },
                    ticks: { precision: 0 }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
@endsection
