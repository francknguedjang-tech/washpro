{{-- Nom : admin/depots/create.blade.php | Rôle : Formulaire de création d'un nouveau dépôt avec recherche client --}}
@extends(Auth::user()->role === 'admin' ? 'layouts.admin' : 'layouts.receptionniste')

@section('content')
<div class="container-fluid px-3 py-4">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <h2 class="fw-bold text-dark mb-1 h3">Nouveau Dépôt</h2>
            <p class="text-muted mb-0 small">Enregistrez une nouvelle commande client.</p>
        </div>
        <div class="col-auto">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary rounded-pill px-3 px-md-4 btn-sm fw-bold">
                <i class="bi bi-arrow-left me-2"></i> <span class="d-none d-md-inline">Retour</span>
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm rounded-4 mb-4 border-0">
            <div class="d-flex align-items-center mb-2">
                <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                <h6 class="mb-0 fw-bold">Erreur de validation</h6>
            </div>
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('depots.store') }}" method="POST" id="depotForm">
        @csrf
        <div class="row g-4">
            {{-- Colonne Gauche : Sélection du client, date de retrait et paiement initial --}}
            <div class="col-12 col-xl-4 order-2 order-xl-1">
                <div class="card border-0 shadow-sm rounded-4 sticky-xl-top" style="top: 2rem; z-index: 10;">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <h6 class="mb-0 fw-bold text-primary">
                            <i class="bi bi-person-lines-fill me-2"></i> 1. Client & Paramètres
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <!-- Recherche Rapide -->
                        <div class="mb-4">
                            <label class="form-label small text-muted fw-bold text-uppercase">Client (Recherche Rapide)</label>
                            <div class="input-group position-relative">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-search text-primary"></i></span>
                                <input type="text" id="recherche_input" class="form-control bg-light border-0 py-2" placeholder="Nom ou Téléphone..." autocomplete="off">
                                <input type="hidden" name="client_id" id="client_id_hidden" required>
                                <div id="suggestions_box" class="list-group position-absolute w-100 shadow-lg d-none" style="top: 100%; z-index: 9999; border-radius: 0.5rem; overflow: hidden;"></div>
                            </div>
                            
                            <div class="mt-2 text-end">
                                <button type="button" class="btn btn-sm btn-link text-primary p-0 text-decoration-none fw-semibold" data-bs-toggle="modal" data-bs-target="#modalNouveauClient">
                                    <i class="bi bi-plus-circle-fill"></i> Nouveau client ?
                                </button>
                            </div>
                        </div>

                        <!-- Date Retrait -->
                        <div class="mb-4">
                            <label class="form-label small text-muted fw-bold d-flex align-items-center text-primary mb-2 text-uppercase">
                                Date de retrait prévue
                            </label>
                            <div class="input-group">
                                <span class="input-group-text border-primary bg-primary bg-opacity-10 text-primary border-end-0"><i class="bi bi-calendar-event"></i></span>
                                <input type="datetime-local" name="date_retrait" id="date_retrait" class="form-control border-primary bg-primary bg-opacity-10 text-primary fw-medium border-start-0 ps-0" required>
                            </div>
                        </div>

                        <hr class="border-light my-4">

                        <!-- Immediate Payment Section -->
                        <div class="mb-2">
                            <label class="form-label small text-muted fw-bold d-flex align-items-center text-success mb-2 text-uppercase">
                                <i class="bi bi-wallet2 me-2"></i> 2. Paiement immédiat
                            </label>
                            
                            <div class="p-3 bg-soft-success rounded-3 border border-success border-opacity-25">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-success mb-1">Montant versé (FCFA)</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" name="montant_paye" id="montant_paye" class="form-control fw-bold text-success border-success" placeholder="0" min="0">
                                        <button class="btn btn-success fw-bold px-3" type="button" id="btnPayAll" data-bs-toggle="tooltip" title="Payer la totalité">MAX</button>
                                    </div>
                                </div>
                                <div>
                                    <label class="form-label small fw-bold text-success mb-1">Mode de paiement</label>
                                    <select name="mode_paiement" class="form-select form-select-sm border-success fw-medium text-success bg-white">
                                        <option value="cache">Espèces (Cache)</option>
                                        <option value="orange_money">Orange Money</option>
                                        <option value="mobile_money">Mobile Money</option>
                                    </select>
                                </div>
                            </div>
                            <small class="text-muted fst-italic mt-2 d-block text-center" style="font-size: 0.75rem;">Laissez vide si paiement à la livraison.</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Colonne Droite : Sélection des articles et services --}}
            <div class="col-12 col-xl-8 order-1 order-xl-2">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h6 class="mb-0 fw-bold text-primary fs-5">
                            <i class="bi bi-box-seam-fill me-2"></i> 3. Articles & Services
                        </h6>
                        <button type="button" class="btn btn-outline-primary rounded-pill px-4 fw-bold d-flex align-items-center" id="addArticle">
                            <i class="bi bi-plus-lg me-2"></i> Ajouter un article
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="articlesTable">
                                <thead class="bg-light">
                                    <tr class="small text-muted text-uppercase fw-bold">
                                        <th class="ps-4 border-0 py-3" style="width: 45%;">Désignation</th>
                                        <th class="border-0 py-3" style="width: 20%;">Service</th>
                                        <th class="border-0 text-center py-3" style="width: 90px;">Qté</th>
                                        <th class="border-0 text-end py-3" style="width: 100px;">P.U</th>
                                        <th class="border-0 text-end py-3" style="width: 120px;">Total</th>
                                        <th class="pe-4 border-0 py-3" style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="article-row border-bottom border-light">
                                        <td class="ps-4 py-3">
                                            <input type="text" name="articles[0][description]" class="form-control form-control-lg border-0 bg-light-subtle rounded-3 fw-bold fs-6" placeholder="Ex: Chemise..." required>
                                        </td>
                                        <td class="py-3">
                                            <select name="articles[0][service_id]" class="form-select form-select-lg border-0 bg-light-subtle rounded-3 fw-bold fs-6 service-select" required>
                                                <option value="">Sélectionner...</option>
                                                @foreach($services as $service)
                                                    <option value="{{ $service->id }}" data-price="{{ $service->prix_unitaire }}" data-unit="{{ $service->unite }}">
                                                        {{ $service->libelle }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="py-3">
                                            <input type="number" name="articles[0][quantite]" class="form-control form-control-lg border-0 bg-light-subtle text-center fw-black text-primary fs-5 qte-input rounded-3" value="1" min="0.1" step="0.1" required>
                                        </td>
                                        <td class="py-3 text-end text-muted fw-bold">
                                            <span class="unit-price">0</span> F
                                        </td>
                                        <td class="py-3 text-end fw-black text-dark fs-5 line-total" data-value="0">
                                            0 F
                                        </td>
                                        <td class="pe-4 py-3 text-end">
                                            <button type="button" class="btn btn-light text-danger p-2 rounded-3 remove-row border-0 shadow-sm hover-lift" tabindex="-1"><i class="bi bi-x-lg"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Empty State (Hidden) -->
                        <div id="emptyState" class="text-center py-5 d-none bg-light-subtle m-3 rounded-4 border-dashed">
                            <i class="bi bi-cart-x text-muted opacity-25" style="font-size: 4rem;"></i>
                            <p class="text-muted mt-3 fw-bold fs-5 mb-4">Aucun article sélectionné.</p>
                            <div>
                                <button type="button" class="btn btn-outline-primary btn-lg rounded-pill px-5 fw-bold shadow-sm hover-lift" id="addFirstArticle">
                                    <i class="bi bi-plus-lg me-2"></i> Commencer à ajouter
                                </button>
                            </div>
                        </div>

                        <!-- Totals & Actions Footer -->
                        <div class="bg-light border-top mt-4" style="border-bottom-left-radius: 1rem; border-bottom-right-radius: 1rem;">
                            <!-- Totals Banner -->
                            <div class="p-3 px-4 d-flex justify-content-between align-items-center border-bottom bg-white">
                                <div class="d-flex flex-column">
                                    <span class="badge bg-soft-primary text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1 mb-1 d-inline-block fit-content" id="itemCount">1 article</span>
                                    <span class="text-muted small fw-medium">Sous-total : <span id="subtotal" class="fw-bold text-dark">0 F</span></span>
                                </div>
                                <div class="text-end bg-light-subtle p-2 px-3 rounded-3 border">
                                    <span class="text-uppercase small fw-bold text-muted d-block mb-1" style="font-size: 0.70rem;">Total NET À PAYER</span>
                                    <span class="fw-black fs-3 text-primary" id="grandTotal" style="letter-spacing: -0.5px;">0 F</span>
                                </div>
                            </div>
                            
                            <!-- Form Actions -->
                            <div class="p-4 d-flex justify-content-between align-items-center flex-wrap gap-3 bg-white" style="border-bottom-left-radius: 1rem; border-bottom-right-radius: 1rem;">
                                <a href="{{ url()->previous() }}" class="btn btn-light border btn-lg rounded-pill px-5 fw-bold text-muted order-2 order-sm-1 shadow-sm hover-lift">
                                    <i class="bi bi-x-lg me-2"></i>Annuler
                                </a>
                                <button type="submit" id="submitBtn" class="btn btn-primary btn-lg rounded-pill px-5 fw-black shadow-lg order-1 order-sm-2 ms-auto hover-lift border-0 fs-5" style="background: linear-gradient(135deg, #2563eb, #1d4ed8); transition: all 0.3s; padding-top: 12px; padding-bottom: 12px;">
                                    <span id="btnText">VALIDER LE DÉPÔT</span>
                                    <span id="btnSpinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                                    <i id="btnIcon" class="bi bi-check2-circle fs-4 ms-2 align-middle"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Scripts Javascript pour la gestion dynamique du formulaire (Ajout d'articles, AJAX Client) --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const depotForm = document.getElementById('depotForm');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnSpinner = document.getElementById('btnSpinner');
    const btnIcon = document.getElementById('btnIcon');

    if (depotForm) {
        depotForm.addEventListener('submit', function(e) {
            // Validation personnalisée : S'assurer qu'il y a au moins un article
            const rows = document.querySelectorAll('.article-row');
            if (rows.length === 0) {
                e.preventDefault();
                Swal.fire({
                    title: 'Panier vide',
                    text: 'Veuillez ajouter au moins un article avant de valider le dépôt.',
                    icon: 'warning',
                    confirmButtonColor: '#2563eb',
                    customClass: { popup: 'rounded-4' }
                });
                return false;
            }
            
            // Validation personnalisée : S'assurer qu'un client est sélectionné
            const clientId = document.getElementById('client_id_hidden').value;
            if (!clientId) {
                e.preventDefault();
                Swal.fire({
                    title: 'Client manquant',
                    text: 'Veuillez sélectionner ou créer un client pour ce dépôt.',
                    icon: 'warning',
                    confirmButtonColor: '#2563eb',
                    customClass: { popup: 'rounded-4' }
                });
                return false;
            }

            // Empêcher le double clic
            submitBtn.disabled = true;
            btnText.innerText = "TRAITEMENT EN COURS...";
            btnSpinner.classList.remove('d-none');
            btnIcon.classList.add('d-none');
        });
    }

    let articleCount = 1;
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
                    <input type="text" name="articles[${articleCount}][description]" class="form-control form-control-lg border-0 bg-light-subtle rounded-3 fw-bold fs-6" placeholder="Ex: Chemise..." required>
                </td>
                <td class="py-3">
                    <select name="articles[${articleCount}][service_id]" class="form-select form-select-lg border-0 bg-light-subtle rounded-3 fw-bold fs-6 service-select" required>
                        <option value="">Sélectionner...</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" data-price="{{ $service->prix_unitaire }}" data-unit="{{ $service->unite }}">
                                {{ $service->libelle }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td class="py-3">
                    <input type="number" name="articles[${articleCount}][quantite]" class="form-control form-control-lg border-0 bg-light-subtle text-center fw-black text-primary fs-5 qte-input rounded-3" value="1" min="0.1" step="0.1" required>
                </td>
                <td class="py-3 text-end text-muted fw-bold">
                    <span class="unit-price">0</span> F
                </td>
                <td class="py-3 text-end fw-black text-dark fs-5 line-total" data-value="0">
                    0 F
                </td>
                <td class="pe-4 py-3 text-end">
                    <button type="button" class="btn btn-light text-danger p-2 rounded-3 remove-row border-0 shadow-sm hover-lift" tabindex="-1"><i class="bi bi-x-lg"></i></button>
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
            const row = e.target.closest('.article-row');
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
                
                row.querySelector('.line-total').innerText = lineTotal.toLocaleString() + " F";
                row.querySelector('.line-total').dataset.value = lineTotal;
            } else {
                row.querySelector('.unit-price').innerText = "0";
                row.querySelector('.line-total').innerText = "0 F";
                row.querySelector('.line-total').dataset.value = 0;
            }
            calculateTotals();
        }
    });

    function calculateTotals() {
        let total = 0;
        let count = 0;
        document.querySelectorAll('.line-total').forEach(el => {
            total += parseFloat(el.dataset.value) || 0;
            count++;
        });
        document.getElementById('subtotal').innerText = total.toLocaleString() + " F";
        document.getElementById('grandTotal').innerText = total.toLocaleString() + " F";
        document.getElementById('itemCount').innerText = count + (count > 1 ? " articles" : " article");
        
        // Update the 'Tout payer' button logic logic
        const btnPayAll = document.getElementById('btnPayAll');
        if (btnPayAll) {
            btnPayAll.dataset.total = total;
        }
    }

    // Auto-fill total amount
    document.getElementById('btnPayAll')?.addEventListener('click', function() {
        const total = parseFloat(this.dataset.total) || 0;
        if(total > 0) {
            document.getElementById('montant_paye').value = total;
        }
    });

    // --- Logic Client (Recherche AJAX) ---
    const rechercheInput = document.getElementById('recherche_input');
    const suggestionsBox = document.getElementById('suggestions_box');
    const clientIdHidden = document.getElementById('client_id_hidden');
    let searchTimeout = null;

    rechercheInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            suggestionsBox.classList.add('d-none');
            clientIdHidden.value = '';
            // Retirer le spinner si on vide
            const icon = this.previousElementSibling.querySelector('i');
            if(icon) {
                icon.className = 'bi bi-search text-primary';
            }
            return;
        }

        // Ajouter spinner visuel professionnel
        const icon = this.previousElementSibling.querySelector('i');
        if(icon) {
            icon.className = 'spinner-border spinner-border-sm text-primary';
        }

        searchTimeout = setTimeout(() => {
            fetch(`/api/clients/recherche/${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    // Remettre l'icône de recherche
                    if(icon) icon.className = 'bi bi-search text-primary';
                    
                    suggestionsBox.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(client => {
                            const item = document.createElement('a');
                            item.href = "#";
                            item.className = "list-group-item list-group-item-action py-2";
                            item.innerHTML = `<i class="bi bi-person me-2"></i> <strong>${client.nom.toUpperCase()} ${client.prenom}</strong> (${client.telephone})`;
                            
                            item.onclick = function(e) {
                                e.preventDefault();
                                selectClient(client);
                            };
                            suggestionsBox.appendChild(item);
                        });
                        suggestionsBox.classList.remove('d-none');
                    } else {
                        suggestionsBox.innerHTML = '<div class="list-group-item text-muted">Aucun client trouvé</div>';
                        suggestionsBox.classList.remove('d-none');
                    }
                })
                .catch(() => {
                    if(icon) icon.className = 'bi bi-search text-primary';
                });
        }, 300);
    });

    function selectClient(client) {
        rechercheInput.value = `${client.nom.toUpperCase()} ${client.prenom} (${client.telephone})`;
        clientIdHidden.value = client.id;
        suggestionsBox.classList.add('d-none');
    }

    // --- Logic Client (Création AJAX) ---
    const formNouveauClient = document.getElementById('formNouveauClient');
    if (formNouveauClient) {
        formNouveauClient.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enregistrement...';
            btn.disabled = true;

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Auto-sélectionner le nouveau client
                    selectClient(data.client);
                    
                    // Cerrar el modal de forma robusta
                    const modalElement = document.getElementById('modalNouveauClient');
                    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
                    modal.hide();
                    
                    // Reset le form
                    formNouveauClient.reset();
                    
                    // Notification simple
                    alert('Client créé et sélectionné avec succès.');
                } else {
                    alert('Erreur lors de la création du client.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Une erreur est survenue.');
            })
            .finally(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        });
    }

    // Wrap the old alert for professional look
    window.originalAlert = window.alert;
    window.alert = function(msg) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Information',
                text: msg,
                icon: 'info',
                confirmButtonColor: '#2563eb',
                customClass: { popup: 'rounded-4' }
            });
        } else {
            window.originalAlert(msg);
        }
    };

    document.addEventListener('click', function(e) {
        if (!rechercheInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
            suggestionsBox.classList.add('d-none');
        }
    });

    // Set default date to today + 3 days at 18:00
    const now = new Date();
    now.setDate(now.getDate() + 3);
    now.setHours(18, 0, 0, 0);
    const dateInput = document.getElementById('date_retrait');
    dateInput.value = now.toISOString().slice(0, 16);

    // Initial total calculation
    calculateTotals();
});
</script>

<!-- Modal Nouveau Client -->
<div class="modal fade" id="modalNouveauClient" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1.5rem;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold ps-2">Nouveau Client</h5>
                <button type="button" class="btn-close me-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('clients.store') }}" method="POST" id="formNouveauClient">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Nom</label>
                        <input type="text" name="nom" class="form-control bg-light border-0 py-2 px-3 rounded-3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Prénom</label>
                        <input type="text" name="prenom" class="form-control bg-light border-0 py-2 px-3 rounded-3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Téléphone</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 rounded-start-3 text-muted px-3">+237</span>
                            <input type="tel" name="telephone" class="form-control bg-light border-0 py-2 px-3 rounded-end-3" required placeholder="01 02 03 04 05">
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold text-muted">Email (Optionnel)</label>
                        <input type="email" name="email" class="form-control bg-light border-0 py-2 px-3 rounded-3" placeholder="client@exemple.com">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 p-4">
                    <button type="button" class="btn btn-light fw-bold px-4 py-2 rounded-3" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4 py-2 rounded-3">Enregistrer le client</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .table .form-control, .table .form-select { min-height: 56px !important; padding-top: 14px !important; padding-bottom: 14px !important; font-size: 1.05rem !important; }
    body { background-color: #f3f4f6; }
    .bg-light { background-color: #f8fafc !important; }
    .bg-light-subtle { background-color: #f1f5f9 !important; }
    .card { border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important; }
    .form-control:focus, .form-select:focus { box-shadow: none; border-color: #3b82f6; background-color: #fff !important; }
    .table th { font-weight: 700; font-size: 0.75rem; letter-spacing: 0.05em; border: none; }
    .btn-primary { background-color: #2563eb; border-color: #2563eb; }
    .btn-primary:hover { border-color: #1d4ed8; }
    
    .border-dashed { border: 2px dashed #e2e8f0; }
    .fit-content { width: fit-content; }
    .hover-lift { transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
    .hover-lift:hover { transform: translateY(-2px); }
    
    .text-primary { color: #2563eb !important; }
    .sticky-xl-top { transition: all 0.3s ease; }
    
    @media (max-width: 768px) {
        .btn-lg { width: 100%; border-radius: 0.75rem; }
        .article-row td { min-width: 140px; }
        .article-row td:first-child { min-width: 200px; }
    }
    
    .fw-black { font-weight: 900; }
    .bg-soft-success { background-color: rgba(46, 204, 113, 0.08); }
    .bg-soft-primary { background-color: rgba(37, 99, 235, 0.08); }

    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: #f1f1f1; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
@endsection