@extends(Auth::user()->role === 'admin' ? 'layouts.admin' : 'layouts.receptionniste')

@section('content')
<div class="container-fluid px-3 py-4">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <h2 class="fw-bold text-dark mb-1 h3">Modifier le Dépôt <span class="text-primary">{{ $depot->reference }}</span></h2>
            <p class="text-muted mb-0 small">Mettez à jour les informations du dépôt client.</p>
        </div>
        <div class="col-auto">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary rounded-pill px-3 px-md-4 btn-sm fw-bold">
                <i class="bi bi-arrow-left me-2"></i> <span class="d-none d-md-inline">Retour</span>
            </a>
        </div>
    </div>

    <form action="{{ route('depots.update', $depot->id) }}" method="POST" id="depotForm">
        @csrf
        @method('PATCH')
        <div class="row g-4">
            <!-- Left Column: Client Info -->
            <div class="col-12 col-xl-4 order-2 order-xl-1">
                <div class="card border-0 shadow-sm rounded-4 sticky-xl-top" style="top: 2rem; z-index: 10;">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <h6 class="mb-0 fw-bold text-primary">
                            <i class="bi bi-person-fill me-2"></i> 1. Informations Client
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-4 mb-4">
                            <div class="avatar-md bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 48px; height: 48px; flex-shrink: 0;">
                                {{ strtoupper(substr($depot->client->prenom, 0, 1) . substr($depot->client->nom, 0, 1)) }}
                            </div>
                            <div class="overflow-hidden">
                                <h6 class="fw-bold mb-0 text-truncate">{{ $depot->client->prenom }} {{ $depot->client->nom }}</h6>
                                <small class="text-muted">{{ $depot->client->telephone }}</small>
                            </div>
                        </div>

                        <!-- Date Retrait -->
                        <div class="mt-4 pt-3 border-top">
                            <label class="form-label small text-muted fw-bold d-flex align-items-center text-primary mb-2">
                                <i class="bi bi-calendar-event me-2"></i> Date de retrait prévue
                            </label>
                            <input type="datetime-local" name="date_retrait" id="date_retrait" 
                                   class="form-control border-primary bg-primary bg-opacity-10 text-primary fw-medium rounded-3" 
                                   value="{{ date('Y-m-d\TH:i', strtotime($depot->date_retrait_prevue)) }}" required>
                        </div>

                        <!-- Poids Global -->
                        <div class="mt-4 pt-3 border-top">
                            <label class="form-label small text-muted fw-bold d-flex align-items-center text-dark mb-2 text-uppercase">
                                <i class="bi bi-scales me-2"></i> Poids Total (kg)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-scales"></i></span>
                                <input type="number" name="poid" id="poid" class="form-control bg-light border-start-0 fw-bold" placeholder="0.0" step="0.1" min="0" value="{{ $depot->poid }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Articles -->
            <div class="col-12 col-xl-8 order-1 order-xl-2">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h6 class="mb-0 fw-bold text-primary">
                            <i class="bi bi-box-seam-fill me-2"></i> 2. Articles & Services
                        </h6>
                        <button type="button" class="btn btn-success btn-sm rounded-pill px-3 d-flex align-items-center" id="addArticle">
                            <i class="bi bi-plus-lg me-1"></i> <span class="small fw-bold">Ajouter un article</span>
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0" id="articlesTable">
                                <thead class="bg-light">
                                    <tr class="small text-muted text-uppercase">
                                        <th class="ps-4 border-0" style="width: 45%;">Désignation</th>
                                        <th class="border-0" style="width: 20%;">Service</th>
                                        <th class="border-0 text-center" style="width: 90px;">Qté</th>
                                        <th class="border-0 text-end" style="width: 100px;">P.U</th>
                                        <th class="border-0 text-end" style="width: 120px;">Total</th>
                                        <th class="pe-4 border-0" style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($depot->linges as $index => $linge)
                                    <tr class="article-row border-bottom border-light">
                                        <td class="ps-4 py-3">
                                            <input type="text" name="articles[{{ $index }}][description]" 
                                                   class="form-control form-control-sm border-0 bg-light-subtle rounded-3" 
                                                   value="{{ $linge->description }}" required>
                                        </td>
                                        <td class="py-3">
                                            <select name="articles[{{ $index }}][service_id]" 
                                                    class="form-select form-select-sm border-0 bg-light rounded-3 service-select" required>
                                                @foreach($services as $service)
                                                    <option value="{{ $service->id }}" 
                                                            data-price="{{ $service->prix_unitaire }}" 
                                                            data-unit="{{ $service->unite }}"
                                                            {{ $linge->service_id == $service->id ? 'selected' : '' }}>
                                                        {{ $service->libelle }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="py-3">
                                            <input type="number" name="articles[{{ $index }}][quantite]" 
                                                   class="form-control form-control-sm border-0 bg-light text-center qte-input rounded-3" 
                                                   value="{{ $linge->quantite }}" min="0.1" step="0.1" required>
                                        </td>
                                        <td class="py-3 text-end text-muted small">
                                            <span class="unit-price">{{ number_format($linge->prix_unitaire, 0, ',', ' ') }}</span> F
                                        </td>
                                        <td class="py-3 text-end fw-bold text-primary line-total" 
                                            data-value="{{ $linge->quantite * $linge->prix_unitaire }}">
                                            {{ number_format($linge->quantite * $linge->prix_unitaire, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="pe-4 py-3 text-end">
                                            <button type="button" class="btn btn-link text-danger p-0 remove-row border-0"><i class="bi bi-trash-fill"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Empty State (Hidden) -->
                        <div id="emptyState" class="text-center py-5 d-none">
                            <i class="bi bi-cart-x text-muted opacity-25" style="font-size: 4rem;"></i>
                            <p class="text-muted mt-3 mb-0">Aucun article ajouté.</p>
                            <button type="button" class="btn btn-primary btn-sm mt-3 rounded-pill px-4" id="addFirstArticle">
                                <i class="bi bi-plus-lg me-1"></i> Ajouter le premier article
                            </button>
                        </div>

                        <!-- Totals Section -->
                        <div class="p-4 bg-light bg-opacity-50 border-top mt-auto">
                            <div class="row g-3 justify-content-end">
                                <div class="col-12 col-md-6 col-lg-5">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted small">Sous-total</span>
                                        <span class="fw-bold text-dark" id="subtotal">0 FCFA</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="text-muted small">Total Articles</span>
                                        <span class="badge bg-secondary rounded-pill" id="itemCount">0</span>
                                    </div>
                                    <hr class="my-3 opacity-10">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-dark text-uppercase">Total mis à jour</span>
                                        <span class="fw-bold fs-3 text-primary" id="grandTotal">0 FCFA</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="p-4 border-top d-flex justify-content-end gap-3 flex-wrap">
                            <a href="{{ url()->previous() }}" class="btn btn-light rounded-pill px-4 text-muted fw-bold order-2 order-sm-1">Annuler</a>
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow-lg order-1 order-sm-2 w-100 w-sm-auto">
                                <i class="bi bi-check-circle-fill me-2"></i> ENREGISTRER LES MODIFICATIONS
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let articleCount = {{ count($depot->linges) }};
    const tableBody = document.querySelector('#articlesTable tbody');
    const emptyState = document.getElementById('emptyState');
    const tableContainer = document.querySelector('.table-responsive');

    function updateEmptyState() {
        const rows = document.querySelectorAll('.article-row');
        if (rows.length === 0) {
            tableContainer.classList.add('d-none');
            emptyState.classList.remove('d-none');
        } else {
            tableContainer.classList.remove('d-none');
            emptyState.classList.add('d-none');
        }
    }

    // Add Row
    function addRow() {
        const newRow = `
            <tr class="article-row border-bottom border-light">
                <td class="ps-4 py-3">
                    <input type="text" name="articles[${articleCount}][description]" class="form-control form-control-sm border-0 bg-light-subtle rounded-3" placeholder="Désignation..." required>
                </td>
                <td class="py-3">
                    <select name="articles[${articleCount}][service_id]" class="form-select form-select-sm border-0 bg-light rounded-3 service-select" required>
                        <option value="">Sélectionner...</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" data-price="{{ $service->prix_unitaire }}" data-unit="{{ $service->unite }}">
                                {{ $service->libelle }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td class="py-3">
                    <input type="number" name="articles[${articleCount}][quantite]" class="form-control form-control-sm border-0 bg-light text-center qte-input rounded-3" value="1" min="0.1" step="0.1" required>
                </td>
                <td class="py-3 text-end text-muted small">
                    <span class="unit-price">0</span> F
                </td>
                <td class="py-3 text-end fw-bold text-primary line-total" data-value="0">
                    0 FCFA
                </td>
                <td class="pe-4 py-3 text-end">
                    <button type="button" class="btn btn-link text-danger p-0 remove-row border-0"><i class="bi bi-trash-fill"></i></button>
                </td>
            </tr>
        `;
        tableBody.insertAdjacentHTML('beforeend', newRow);
        articleCount++;
        updateEmptyState();
        calculateTotals();
    }

    document.getElementById('addArticle').addEventListener('click', addRow);
    document.getElementById('addFirstArticle')?.addEventListener('click', addRow);

    // Remove Row
    document.getElementById('articlesTable').addEventListener('click', function(e) {
        if (e.target.closest('.remove-row')) {
            e.target.closest('.article-row').remove();
            updateEmptyState();
            calculateTotals();
        }
    });

    // Calculate per line
    document.getElementById('articlesTable').addEventListener('input', function(e) {
        if (e.target.closest('.service-select') || e.target.closest('.qte-input')) {
            updateRowTotal(e.target.closest('.article-row'));
            calculateTotals();
        }
    });

    function updateRowTotal(row) {
        const select = row.querySelector('.service-select');
        const qte = parseFloat(row.querySelector('.qte-input').value) || 0;
        const option = select.options[select.selectedIndex];
        
        if (option && option.value) {
            const price = parseFloat(option.dataset.price);
            row.querySelector('.unit-price').innerText = price.toLocaleString();
            
            let lineTotal = 0;
            const unit = (option.dataset.unit || '').toLowerCase();
            if (unit.includes('kg')) {
                const entier = Math.floor(qte);
                const decimale = Number((qte - entier).toFixed(2));
                if (decimale > 0.6) {
                    lineTotal = Math.ceil(qte) * price;
                } else if (decimale > 0) {
                    lineTotal = (Math.ceil(qte) * price) - 500;
                } else {
                    lineTotal = qte * price;
                }
            } else {
                lineTotal = qte * price;
            }
            
            if (lineTotal < 500 && lineTotal > 0) lineTotal = 500;
            
            row.querySelector('.line-total').innerText = lineTotal.toLocaleString() + " FCFA";
            row.querySelector('.line-total').dataset.value = lineTotal;
        } else {
            row.querySelector('.unit-price').innerText = "0";
            row.querySelector('.line-total').innerText = "0 FCFA";
            row.querySelector('.line-total').dataset.value = 0;
        }
    }

    function calculateTotals() {
        let total = 0;
        let count = 0;
        document.querySelectorAll('.line-total').forEach(el => {
            total += parseFloat(el.dataset.value) || 0;
            count++;
        });
        document.getElementById('subtotal').innerText = total.toLocaleString() + " FCFA";
        document.getElementById('grandTotal').innerText = total.toLocaleString() + " FCFA";
        document.getElementById('itemCount').innerText = count;
    }

    // Initial total calculation for existing rows
    document.querySelectorAll('.article-row').forEach(row => {
        updateRowTotal(row);
    });
    calculateTotals();
});
</script>

<style>
    .table .form-control, .table .form-select { min-height: 56px !important; padding-top: 14px !important; padding-bottom: 14px !important; font-size: 1.05rem !important; }
    body { background-color: #f3f4f6; }
    .bg-light { background-color: #f8fafc !important; }
    .bg-light-subtle { background-color: #f1f5f9 !important; }
    .card { border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important; }
    .form-control:focus, .form-select:focus { box-shadow: none; border-color: #3b82f6; background-color: #fff !important; }
    .table th { font-weight: 700; font-size: 0.75rem; letter-spacing: 0.05em; border: none; }
    .btn-primary { background-color: #2563eb; border-color: #2563eb; }
    .btn-primary:hover { background-color: #1d4ed8; border-color: #1d4ed8; }
    .text-primary { color: #2563eb !important; }
    
    @media (max-width: 768px) {
        .btn-lg { width: 100%; border-radius: 0.75rem; }
        .article-row td { min-width: 140px; }
        .article-row td:first-child { min-width: 200px; }
    }
</style>
@endsection
