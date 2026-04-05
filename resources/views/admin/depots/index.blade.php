{{-- Nom : admin/depots/index.blade.php | Rôle : Liste et filtrage de tous les dépôts enregistrés --}}
@extends(Auth::user()->role === 'admin' ? 'layouts.admin' : 'layouts.receptionniste')

@section('content')
<div class="container-fluid px-4 py-4 pb-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Gestion des Dépôts</h2>
            <p class="text-muted small">Consultez et gérez l'ensemble des dépôts clients.</p>
        </div>
        <a href="{{ route('depots.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
            <i class="bi bi-plus-lg me-2"></i>Nouveau Dépôt
        </a>
    </div>

    {{-- Barre de filtrage pour affiner la recherche --}}
    <div class="card border-0 shadow-sm mb-3" style="border-radius: 1.5rem;">
        <div class="card-body p-4">
            <form action="{{ route('depots.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small fw-bold text-muted text-uppercase">Recherche Client / Réf</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search text-primary"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-0 py-2" placeholder="Nom, Téléphone ou Référence..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted text-uppercase">Statut</label>
                    <select name="status" class="form-select bg-light border-0 py-2 fw-medium">
                        <option value="">Tous les statuts</option>
                        <option value="en cours" {{ request('status') == 'en cours' ? 'selected' : '' }}>En cours</option>
                        <option value="pret" {{ request('status') == 'pret' ? 'selected' : '' }}>Prêt</option>
                        <option value="recuperer" {{ request('status') == 'recuperer' ? 'selected' : '' }}>Récupéré</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted text-uppercase">Date</label>
                    <input type="date" name="date" class="form-control bg-light border-0 py-2 text-muted fw-medium" value="{{ request('date') }}">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary fw-bold px-3 py-2 rounded-3 w-100 shadow-sm"><i class="bi bi-funnel-fill me-1"></i> Filtrer</button>
                    <a href="{{ route('depots.index') }}" class="btn btn-light fw-bold px-3 py-2 rounded-3 text-muted" title="Réinitialiser"><i class="bi bi-arrow-clockwise"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 24px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                {{-- Tableau principal des dépôts --}}
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="text-muted small">
                            <th class="ps-4 py-3">REF.</th>
                            <th class="py-3">DATE</th>
                            <th class="py-3">CLIENT</th>
                            <th class="py-3">ARTICLES</th>
                            <th class="py-3 text-end">TOTAL</th>
                            <th class="py-3 text-center">PAIEMENT</th>
                            <th class="py-3 text-center">STATUT</th>
                            <th class="pe-4 py-3 text-end">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($depots as $depot)
                        <tr class="border-bottom border-light">
                            <td class="ps-4 py-3 fw-bold text-primary">{{ $depot->reference }}</td>
                            <td class="py-3 text-muted small">{{ \Carbon\Carbon::parse($depot->date_depot)->format('d/m/Y H:i') }}</td>
                            <td class="py-3">
                                <div class="fw-bold text-dark">{{ $depot->client->nom ?? 'Inconnu' }} {{ $depot->client->prenom ?? '' }}</div>
                                <div class="small text-muted"><i class="bi bi-telephone me-1"></i>{{ $depot->client->telephone ?? '-' }}</div>
                            </td>
                            <td class="py-3">
                                @forelse($depot->linges as $linge)
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-light text-dark border small fw-normal">{{ $linge->quantite }}</span>
                                    <span class="small fw-semibold text-muted text-truncate" style="max-width: 150px;">{{ $linge->service->libelle }}</span>
                                </div>
                                @empty
                                <span class="text-muted small">Aucun article</span>
                                @endforelse
                            </td>
                            <td class="py-3 text-end fw-bold text-dark">{{ number_format($depot->prix_total, 0, ',', ' ') }} F</td>
                            <td class="py-3 text-center">
                                @php
                                    $payColor = match($depot->etat_paiement) {
                                        'payé' => 'success',
                                        'partiel' => 'warning',
                                        default => 'danger'
                                    };
                                @endphp
                                <span class="badge bg-soft-{{ $payColor }} text-{{ $payColor == 'warning' ? 'dark' : $payColor }} rounded-pill px-3 border border-{{ $payColor }} border-opacity-25" style="font-size: 0.75rem;">
                                    {{ strtoupper($depot->etat_paiement) }}
                                </span>
                            </td>
                            <td class="py-3 text-center">
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
                                        
                                        @if(Auth::user()->role == 'admin')
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('depots.destroy', $depot->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce dépôt ? Cette action est irréversible.')">
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
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 mb-2 d-block opacity-25"></i>
                                Aucun dépôt trouvé.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($depots->hasPages())
                <div class="px-4 py-3 border-top bg-white border-0">
                    {{ $depots->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .pagination { margin: 0; }
    .page-link { border: none; font-weight: 600; color: #64748b; border-radius: 8px !important; margin: 0 2px; }
    .page-item.active .page-link { background-color: var(--primary-color); box-shadow: 0 4px 10px rgba(67, 97, 238, 0.3); }
    .bg-soft-primary { background-color: #f0f3ff; }
    .bg-soft-success { background-color: rgba(46, 204, 113, 0.1); }
    .bg-soft-warning { background-color: rgba(243, 156, 18, 0.1); }
    .bg-soft-danger { background-color: rgba(231, 76, 60, 0.1); }
    .text-success { color: var(--success-color) !important; }
    .text-warning { color: var(--warning-color) !important; }
    .text-danger { color: var(--danger-color) !important; }
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
                        Toast.fire({ icon: 'success', title: data.message || 'Statut mis à jour.' });
                    }

                    const btn = this.closest('.dropdown').querySelector('.dropdown-toggle');
                    btn.innerHTML = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                    
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
@endsection
