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
    {{-- ======================================================= --}}
    {{-- ZONE DE RECHERCHE INSTANTANÉE PAR CODE DE RETRAIT      --}}
    {{-- ======================================================= --}}
    <div class="card border-0 shadow mb-3" style="border-radius: 1.5rem; border-left: 4px solid #4361ee !important;">
        <div class="card-body p-4">
            <label class="form-label fw-bold text-primary mb-2">
                <i class="bi bi-qr-code-scan me-2"></i> Recherche par Code de Retrait
            </label>
            <div class="input-group input-group-lg shadow-sm" style="max-width: 500px;">
                <span class="input-group-text bg-primary text-white border-0 fw-black px-4" style="letter-spacing:2px; font-size:1.1rem;">WP-</span>
                <input type="text"
                       id="liveCodeSearch"
                       class="form-control border-0 fw-bold text-primary bg-light"
                       placeholder="Tapez le code... (ex: A8KX2)"
                       maxlength="10"
                       style="text-transform:uppercase; letter-spacing: 3px; font-size: 1.2rem;"
                       autocomplete="off">
                <span class="input-group-text bg-light border-0" id="searchSpinner">
                    <i class="bi bi-search text-muted" id="searchIcon"></i>
                </span>
            </div>
            <small class="text-muted mt-1 d-block"><i class="bi bi-info-circle me-1"></i>Résultat affiché instantanément dès la saisie.</small>

            {{-- Carte résultat instantanée --}}
            <div id="liveSearchResult" class="mt-3 d-none">
                {{-- Remplie dynamiquement par JavaScript --}}
            </div>
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
                            <th class="py-3">CODE RETRAIT</th>
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
                            <td class="py-3">
                                <span class="badge px-3 py-2 rounded-pill fw-bold" style="background: #eef2ff; color: #4361ee; font-family: monospace; font-size: 0.9rem; letter-spacing: 2px;">{{ $depot->code_retrait ?? '-' }}</span>
                            </td>
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
                            <td colspan="9" class="text-center py-5 text-muted">
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
// ================================================================
// RECHERCHE INSTANTANÉE PAR CODE DE RETRAIT
// ================================================================
document.addEventListener('DOMContentLoaded', function() {
    const input      = document.getElementById('liveCodeSearch');
    const resultBox  = document.getElementById('liveSearchResult');
    const icon       = document.getElementById('searchIcon');
    let debounceTimer;

    const statColors = {
        'en cours': { bg: '#fff8e1', color: '#f59e0b', label: 'En cours' },
        'pret':     { bg: '#e8f5e9', color: '#16a34a', label: 'Prêt ✅' },
        'recuperer':{ bg: '#e3f2fd', color: '#2563eb', label: 'Récupéré' },
    };
    const payColors = {
        'payé':   { bg: '#e8f5e9', color: '#16a34a' },
        'partiel':{ bg: '#fff8e1', color: '#d97706' },
        'default':{ bg: '#fef2f2', color: '#dc2626' },
    };

    function setLoading(loading) {
        if (loading) {
            icon.className = 'spinner-border spinner-border-sm text-primary';
        } else {
            icon.className = 'bi bi-search text-muted';
        }
    }

    function showResult(depot) {
        const stat   = statColors[depot.etat] || { bg: '#f1f5f9', color: '#64748b', label: depot.etat };
        const pay    = payColors[depot.etat_paiement] || payColors['default'];
        const arts   = depot.articles.join(', ');

        resultBox.innerHTML = `
            <div class="card border-0 shadow-lg animate__animated animate__fadeIn" style="border-radius:16px; border-left: 5px solid ${stat.color} !important;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                        <div>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <span class="badge px-3 py-2 fw-bold rounded-pill" style="background:#eef2ff; color:#4361ee; font-family:monospace; font-size:1rem; letter-spacing:3px;">${depot.code_retrait}</span>
                                <span class="badge px-3 py-2 rounded-pill fw-bold" style="background:${stat.bg}; color:${stat.color};">${stat.label}</span>
                                <span class="badge px-3 py-2 rounded-pill fw-bold" style="background:${pay.bg}; color:${pay.color};">${(depot.etat_paiement).toUpperCase()}</span>
                            </div>
                            <h5 class="fw-black text-dark mb-1">${depot.client}</h5>
                            <p class="text-muted small mb-0"><i class="bi bi-telephone me-1"></i>${depot.telephone}</p>
                        </div>
                        <div class="text-end">
                            <div class="small text-muted">Réf. <strong class="text-primary">${depot.reference}</strong></div>
                            <div class="small text-muted mt-1"><i class="bi bi-calendar me-1"></i>Dépôt : ${depot.date_depot}</div>
                            <div class="small text-muted"><i class="bi bi-clock me-1"></i>Retrait prévu : ${depot.date_retrait}</div>
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="row g-3 align-items-center">
                        <div class="col-md-6">
                            <div class="small text-muted fw-bold text-uppercase mb-1">Articles</div>
                            <div class="fw-semibold text-dark">${arts || 'Aucun article'}</div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="small text-muted fw-bold text-uppercase mb-1">Total</div>
                            <div class="fw-black text-dark" style="font-size:1.2rem;">${depot.prix_total} F</div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="small text-muted fw-bold text-uppercase mb-1">Reste à payer</div>
                            <div class="fw-black" style="font-size:1.2rem; color:${pay.color};">${depot.reste_a_payer} F</div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4 flex-wrap">
                        <a href="${depot.url_detail}" class="btn btn-primary fw-bold rounded-pill px-4">
                            <i class="bi bi-eye me-2"></i>Voir le Dépôt
                        </a>
                        <a href="${depot.url_facture}" target="_blank" class="btn btn-outline-primary fw-bold rounded-pill px-4">
                            <i class="bi bi-printer me-2"></i>Imprimer la Facture
                        </a>
                    </div>
                </div>
            </div>
        `;
        resultBox.classList.remove('d-none');
    }

    function showNotFound() {
        resultBox.innerHTML = `
            <div class="alert alert-warning border-0 rounded-4 d-flex align-items-center gap-3 shadow-sm">
                <i class="bi bi-exclamation-triangle-fill fs-3"></i>
                <div>
                    <strong>Aucun dépôt trouvé</strong><br>
                    <small>Vérifiez le code de retrait saisi. Il est inscrit sur la facture remise au client.</small>
                </div>
            </div>
        `;
        resultBox.classList.remove('d-none');
    }

    input.addEventListener('input', function() {
        const code = this.value.trim().toUpperCase();
        clearTimeout(debounceTimer);

        if (code.length < 2) {
            resultBox.classList.add('d-none');
            resultBox.innerHTML = '';
            setLoading(false);
            return;
        }

        setLoading(true);

        debounceTimer = setTimeout(() => {
            fetch(`{{ route('depots.rechercheCode') }}?code=${encodeURIComponent(code)}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(r => r.json())
            .then(data => {
                setLoading(false);
                if (data.depot) {
                    showResult(data.depot);
                } else {
                    showNotFound();
                }
            })
            .catch(() => { setLoading(false); });
        }, 300);
    });
});
</script>

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
