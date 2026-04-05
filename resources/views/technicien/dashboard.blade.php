{{-- Nom : technicien/dashboard.blade.php | Rôle : Interface de travail du Technicien (File d'attente des lavages) --}}
@extends('layouts.technicien')

@section('content')
<style>
    /* Task Cards - Scoped to technician dashboard */
    .task-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        border-left: 5px solid var(--warning-color, #f1c40f);
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        margin-bottom: 1rem;
        transition: transform 0.2s;
    }

    .task-card:hover {
        transform: translateX(5px);
    }

    .task-card.task-pret {
        border-left-color: var(--success-color, #2ecc71);
        opacity: 0.7;
    }

    .task-date {
        font-size: 0.85rem;
        color: #64748b;
        font-weight: 600;
    }

    .linge-badge {
        background: #f1f5f9;
        color: #475569;
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 500;
        display: inline-block;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
    }
</style>

<div class="container-fluid px-3 py-3">
    {{-- En-tête avec les compteurs de performance du jour --}}
    <header class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-1">Espace Opérationnel</h2>
            <p class="text-muted mb-0">Traitez les commandes clients en cours.</p>
        </div>
        <div class="d-flex gap-4 align-items-center bg-white p-3 rounded-4 shadow-sm">
            <div class="text-center">
                <span class="fs-3 fw-bold text-warning d-block lh-1 mb-1">{{ $stats['en_attente'] }}</span>
                <span class="text-muted small text-uppercase" style="letter-spacing: 0.5px; font-size: 0.7rem;">À faire</span>
            </div>
            <div class="border-start border-2 ps-4 text-center">
                <span class="fs-3 fw-bold text-success d-block lh-1 mb-1">{{ $stats['prets_aujourdhui'] }}</span>
                <span class="text-muted small text-uppercase" style="letter-spacing: 0.5px; font-size: 0.7rem;">Finis (AJD)</span>
            </div>
            <div class="border-start border-2 ps-4 text-center">
                <span class="fs-3 fw-bold text-primary d-block lh-1 mb-1">{{ number_format($stats['poids_aujourdhui'], 1, ',', ' ') }} <small class="fs-6">Kg</small></span>
                <span class="text-muted small text-uppercase" style="letter-spacing: 0.5px; font-size: 0.7rem;">Poids Traité</span>
            </div>
        </div>
    </header>

            <div class="row g-4">
                <!-- Queue / Pending Tasks -->
                <div class="col-lg-8">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold"><i class="bi bi-hourglass-split text-warning me-2"></i> File d'attente prioritaire</h4>
                    </div>

                    @forelse($depotsEnAttente as $depot)
                        <div class="task-card">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <h5 class="fw-bold mb-0">Dépôt #{{ str_pad($depot->id, 5, '0', STR_PAD_LEFT) }}</h5>
                                        @if($depot->total_poids > 0)
                                        <span class="badge bg-soft-primary text-primary rounded-pill border border-primary border-opacity-25" style="background-color: rgba(67, 97, 238, 0.1);"><i class="bi bi-speedometer2 me-1"></i> {{ $depot->total_poids }} Kg</span>
                                        @endif
                                    </div>
                                    <div class="task-date mt-2">
                                        <div class="mb-1"><i class="bi bi-box-arrow-in-right text-muted me-1"></i> <span class="text-muted">Déposé le :</span> <span class="text-dark fw-bold">{{ date('d/m/Y', strtotime($depot->date_depot)) }}</span></div>
                                        <div><i class="bi bi-box-arrow-right text-muted me-1"></i> <span class="text-muted">Retrait prévu :</span> <span class="text-danger fw-bold">{{ date('d/m/Y', strtotime($depot->date_retrait_prevue)) }}</span></div>
                                    </div>
                                </div>
                                
                                {{-- Actions pour changer le statut (En cours -> Prêt) --}}
                                <div class="dropdown">
                                    <button class="btn btn-warning btn-sm rounded-pill fw-bold px-3 dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        En cours
                                    </button>
                                    <ul class="dropdown-menu shadow border-0" style="border-radius: 12px; font-size: 0.85rem;">
                                        <li><h6 class="dropdown-header text-uppercase small text-muted">Action Technicien</h6></li>
                                        <li>
                                            <a class="dropdown-item py-2 fw-bold text-success ajax-status-change" href="#" data-url="{{ route('depots.updateStatus', $depot->id) }}" data-status="pret">
                                                <i class="bi bi-check-circle me-2"></i> Marquer comme Prêt
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            
                            <hr class="opacity-10">
                            
                            {{-- Liste détaillée des articles du client à traiter --}}
                            <div>
                                <p class="small text-muted mb-2 text-uppercase fw-bold letter-spacing">Articles à traiter ({{ $depot->linges->sum('quantite') }}) :</p>
                                @foreach($depot->linges as $linge)
                                    <div class="linge-badge mb-2">
                                        <div class="d-flex align-items-center">
                                            <span class="fw-bold text-primary me-2">{{ $linge->quantite }}x</span>
                                            <span class="fw-semibold">{{ $linge->service->libelle }}</span>
                                        </div>
                                        @if($linge->description)
                                            <div class="small text-muted mt-1 lh-sm" style="font-size: 0.75rem;">
                                                <i class="bi bi-info-circle me-1"></i>{{ $linge->description }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 bg-white rounded-4 shadow-sm border">
                            <i class="bi bi-emoji-smile text-success display-1 mb-3"></i>
                            <h4 class="fw-bold">Excellent travail !</h4>
                            <p class="text-muted">La file d'attente est vide. Toutes les commandes sont traitées.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Historique récent des tâches terminées ce jour --}}
                <div class="col-lg-4">
                    <div class="bg-white rounded-4 p-4 shadow-sm border">
                        <h5 class="fw-bold mb-4"><i class="bi bi-check2-all text-success me-2"></i> Dernièrement récupérés / prêts</h5>
                        
                        <div class="vstack gap-3">
                            @forelse($recentCompleted as $recent)
                                <div class="p-3 bg-light rounded-3 border">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold small">#{{ str_pad($recent->id, 5, '0', STR_PAD_LEFT) }}</span>
                                        <span class="badge bg-success opacity-75 rounded-pill"><i class="bi bi-check-lg"></i></span>
                                    </div>
                                        {{ $recent->linges->sum('quantite') }} articles terminés / prêts.
                                </div>
                            @empty
                                <div class="text-center small text-muted">Aucun dépôt terminé récemment.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
                        const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 1500, timerProgressBar: true });
                        Toast.fire({ icon: 'success', title: 'Tâche terminée et notifiée !' });
                    }

                    const card = this.closest('.task-card');
                    card.style.transition = "all 0.4s";
                    card.style.transform = "translateX(50px)";
                    card.style.opacity = "0";
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            })
            .catch(error => {
                console.error("Error updating status:", error);
                alert("Une erreur est survenue.");
            });
        });
    });
});
</script>
@endsection
