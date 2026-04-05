@extends(Auth::user()->role === 'admin' ? 'layouts.admin' : 'layouts.receptionniste')

@section('content')
<div class="container-fluid px-2 py-3">
    <!-- Header & Status Control Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-3" style="background: linear-gradient(to right, #ffffff, #f8fafc);">
        <div class="card-body p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h3 class="fw-bold text-dark mb-0 d-inline-block me-3">Dépôt <span class="text-primary">{{ $depot->reference }}</span></h3>
                @php
                    $badgeClass = match($depot->etat) {
                        'en cours' => 'warning',
                        'pret' => 'success',
                        'recuperer' => 'primary',
                        default => 'secondary'
                    };
                @endphp
                <span id="statut-badge" class="badge bg-{{ $badgeClass }} px-3 py-1 fs-6 rounded-pill align-text-bottom">{{ ucfirst($depot->etat) }}</span>
                <p class="text-muted mb-0 small mt-1"><i class="bi bi-clock me-1"></i> Retrait prévu: {{ \Carbon\Carbon::parse($depot->date_retrait_prevue)->format('d/m/Y') }} &bull; Créé le {{ \Carbon\Carbon::parse($depot->date_depot)->format('d/m/Y') }} par {{ $depot->receptionniste->prenom ?? 'Système' }}</p>
            </div>
            
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <!-- AJAX Status buttons -->
                @if($depot->etat != 'en cours')
                    <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3 fw-bold status-btn" data-status="en cours" data-id="{{ $depot->id }}">Repasser 'En cours'</button>
                @endif
                @if($depot->etat != 'pret')
                    <button type="button" class="btn btn-sm btn-success rounded-pill px-3 fw-bold shadow-sm status-btn" data-status="pret" data-id="{{ $depot->id }}">Marquer 'Prêt'</button>
                @endif
                @if($depot->etat != 'recuperer')
                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold shadow-sm status-btn" data-status="recuperer" data-id="{{ $depot->id }}">Marquer 'Récupéré'</button>
                @endif
                
                <div class="vr mx-1 opacity-25 d-none d-md-block"></div>
                
                <a href="{{ route('depots.facture', $depot->id) }}" target="_blank" class="btn btn-outline-info rounded-pill px-3 btn-sm text-dark fw-bold">
                    <i class="bi bi-file-earmark-text"></i> Facture
                </a>
                <a href="{{ route('depots.edit', $depot->id) }}" class="btn btn-warning rounded-pill px-3 btn-sm text-white">
                    <i class="bi bi-pencil"></i> Editer
                </a>
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary rounded-pill px-3 btn-sm d-flex align-items-center">
                    <i class="bi bi-arrow-left me-2"></i> Retour
                </a>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="row g-3 print-container">
        <!-- Left Column: Client & Totals -->
        <div class="col-12 col-xl-3">
            <div class="d-flex flex-column gap-3 h-100">
                <!-- Client Info Card -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="avatar bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold fs-5" style="width: 45px; height: 45px;">
                                {{ strtoupper(substr($depot->client->prenom ?? 'C', 0, 1) . substr($depot->client->nom ?? 'L', 0, 1)) }}
                            </div>
                            <div class="overflow-hidden">
                                <h6 class="fw-bold mb-0 text-truncate">{{ $depot->client->prenom ?? 'Client' }} {{ $depot->client->nom ?? 'Inconnu' }}</h6>
                                <span class="badge bg-light text-dark border"><i class="bi bi-telephone me-1"></i>{{ $depot->client->telephone ?? 'N/A' }}</span>
                            </div>
                        </div>
                        @if($depot->client->email)
                        <div class="d-flex align-items-center text-muted small"><i class="bi bi-envelope me-2"></i>{{ $depot->client->email }}</div>
                        @endif
                    </div>
                </div>

                <!-- Financial Summary Card -->
                <div class="card border-0 shadow-sm rounded-4 flex-grow-1">
                    <div class="card-header bg-white border-bottom py-2 px-3">
                        <h6 class="mb-0 fw-bold text-dark small text-uppercase flex-grow-1"><i class="bi bi-calculator me-2"></i>Résumé Financier</h6>
                    </div>
                    <div class="card-body p-3 d-flex flex-column">
                        <div class="d-flex justify-content-between mb-2 small text-muted">
                            <span class="text-uppercase fw-bold">Sous-total:</span>
                            <span class="fw-bold text-dark">{{ number_format($depot->prix_total, 0, ',', ' ') }} F</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom small text-muted">
                            <span class="text-uppercase fw-bold">Déjà payé:</span>
                            <span class="fw-bold text-success">- {{ number_format($depot->paiements->sum('montant'), 0, ',', ' ') }} F</span>
                        </div>
                        <div class="mt-auto bg-light rounded-3 p-3 border {{ $depot->reste_a_payer > 0 ? 'border-warning' : 'border-success' }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-uppercase small {{ $depot->reste_a_payer > 0 ? 'text-warning' : 'text-success' }}">Reste à Payer</span>
                                <span class="fw-bold fs-4 {{ $depot->reste_a_payer > 0 ? 'text-warning' : 'text-success' }}">{{ number_format($depot->reste_a_payer, 0, ',', ' ') }} F</span>
                            </div>
                            <div class="mt-2 text-end">
                                @php
                                    $payBadge = match($depot->etat_paiement) {
                                        'payé' => 'success',
                                        'partiel' => 'info',
                                        default => 'danger'
                                    };
                                @endphp
                                <span class="badge bg-{{ $payBadge }} rounded-pill px-3 py-1 w-100 text-uppercase">{{ $depot->etat_paiement }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Articles & Payments -->
        <div class="col-12 col-xl-9">
            <div class="d-flex flex-column gap-3 h-100">
                <!-- Articles List -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-bottom py-2 px-3">
                        <h6 class="mb-0 fw-bold text-dark small text-uppercase"><i class="bi bi-box-seam-fill me-2 text-primary"></i>Articles Déposés ({{ $depot->linges->sum('quantite') }})</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 35vh; overflow-y: auto;">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead class="bg-light sticky-top" style="z-index: 1;">
                                    <tr class="small text-muted text-uppercase">
                                        <th class="ps-3 border-0 py-2">Article</th>
                                        <th class="border-0 py-2">Service</th>
                                        <th class="border-0 py-2 text-center">Qté</th>
                                        <th class="border-0 py-2 text-end">Px Unit.</th>
                                        <th class="pe-3 border-0 py-2 text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($depot->linges as $linge)
                                    <tr class="border-bottom border-light">
                                        <td class="ps-3 py-2 fw-bold text-dark fs-6">{{ $linge->description }}</td>
                                        <td class="py-2"><span class="badge bg-light text-secondary border fw-normal">{{ $linge->service->libelle }}</span></td>
                                        <td class="py-2 text-center fw-bold">{{ $linge->quantite }} <small class="text-muted fw-normal">{{ $linge->service->unite }}</small></td>
                                        <td class="py-2 text-end text-muted small">{{ number_format($linge->prix_unitaire, 0, ',', ' ') }}</td>
                                        <td class="pe-3 py-2 text-end fw-bold text-primary">{{ number_format($linge->quantite * $linge->prix_unitaire, 0, ',', ' ') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Payments Section -->
                <div class="card border-0 shadow-sm rounded-4 flex-grow-1">
                    <div class="card-header bg-white border-bottom py-2 px-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-dark small text-uppercase"><i class="bi bi-wallet2 me-2 text-success"></i>Paiements</h6>
                        @if($depot->paiements->count() > 0)
                            <a href="{{ route('paiements.recu', $depot->id) }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1" style="font-size: 0.75rem;"><i class="bi bi-receipt me-1"></i>Reçu</a>
                        @endif
                    </div>
                    <div class="card-body p-0 d-flex flex-column flex-md-row">
                        <!-- History List -->
                        <div class="col-md-7 border-end">
                            @if($depot->paiements->count() > 0)
                            <div class="table-responsive" style="max-height: 25vh; overflow-y: auto;">
                                <table class="table table-sm align-middle mb-0 table-hover">
                                    <thead class="bg-light sticky-top">
                                        <tr class="small text-muted text-uppercase fw-bold">
                                            <th class="ps-3 py-2 border-0">Date</th>
                                            <th class="py-2 border-0 text-center">Mode</th>
                                            <th class="py-2 border-0 text-end">Montant</th>
                                            <th class="pe-3 py-2 border-0 text-center">Del</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($depot->paiements as $paiement)
                                        <tr class="border-bottom border-light">
                                            <td class="ps-3 py-2 text-muted small"><i class="bi bi-calendar-event me-1"></i>{{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') }}</td>
                                            <td class="py-2 text-center"><span class="badge bg-secondary px-2 py-1 rounded-pill fw-normal" style="font-size: 0.65rem;">{{ str_replace('_', ' ', $paiement->mode_paiement) }}</span></td>
                                            <td class="py-2 text-end fw-bold text-success small">+{{ number_format($paiement->montant, 0, ',', ' ') }}</td>
                                            <td class="pe-3 py-2 text-center">
                                                <form action="{{ route('paiements.destroy', $paiement->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce paiement ?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-link text-danger p-0 m-0"><i class="bi bi-trash-fill"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="p-4 text-center text-muted h-100 d-flex flex-column align-items-center justify-content-center">
                                <i class="bi bi-journal-text fs-3 mb-2 opacity-50"></i>
                                <p class="small mb-0">Aucun paiement enregistré.</p>
                            </div>
                            @endif
                        </div>
                        <!-- Payment Form -->
                        <div class="col-md-5 p-3 bg-light bg-opacity-50">
                            @if($depot->reste_a_payer > 0)
                            <h6 class="fw-bold mb-3 small text-muted text-uppercase">Nouveau Paiement</h6>
                            <form action="{{ route('paiements.store') }}" method="POST" class="d-flex flex-column gap-2">
                                @csrf
                                <input type="hidden" name="depot_id" value="{{ $depot->id }}">
                                <div class="input-group input-group-sm mb-1">
                                    <span class="input-group-text bg-white fw-bold">F</span>
                                    <input type="number" name="montant" class="form-control fw-bold text-primary" max="{{ $depot->reste_a_payer }}" value="{{ $depot->reste_a_payer }}" required>
                                </div>
                                <select name="mode_paiement" class="form-select form-select-sm mb-2" required>
                                    <option value="cache">Espèces</option>
                                    <option value="orange_money">Orange Money</option>
                                    <option value="mobile_money">Mobile Money</option>
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold shadow-sm rounded-pill"><i class="bi bi-plus-circle me-1"></i>Enregistrer</button>
                            </form>
                            @else
                                <div class="h-100 d-flex align-items-center justify-content-center text-success fw-bold">
                                    <div class="text-center">
                                        <i class="bi bi-check-circle-fill fs-1 mb-2"></i>
                                        <div class="text-uppercase tracking-wider">Facture Soldée</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    body { background-color: #f1f5f9; }
    .card { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important; }
    .table th { font-weight: 700; letter-spacing: 0.03em; }
    
    @media print {
        body { background-color: white !important; }
        nav, .navbar, aside, .btn, form { display: none !important; }
        .print-container { width: 100%; margin: 0; padding: 0; }
        .card { box-shadow: none !important; border: 1px solid #ddd !important; margin-bottom: 20px; }
        .sticky-top { position: static !important; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if(!csrfToken) return;

        document.querySelectorAll('.status-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const status = this.getAttribute('data-status');
                const depotId = this.getAttribute('data-id');
                const url = `/admin/depots/${depotId}/status`;

                // Show loading SweetAlert
                Swal.fire({
                    title: 'Mise à jour...',
                    text: 'Envoi des notifications en arrière-plan',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ status: status })
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Statut modifié',
                            text: 'Email envoyé avec succès !',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        setTimeout(() => window.location.reload(), 2000); // Reload to reflect button updates cleanly
                    } else {
                        Swal.fire('Erreur', data.message || 'Une erreur est survenue', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Fallback to normal form submission if fetch fails due to routing/CORS
                    Swal.fire('Connexion', 'Mise à jour via formulaire...', 'info');
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.innerHTML = `<input type="hidden" name="_token" value="${csrfToken}">
                                      <input type="hidden" name="_method" value="PATCH">
                                      <input type="hidden" name="status" value="${status}">`;
                    document.body.appendChild(form);
                    form.submit();
                });
            });
        });
    });
</script>
@endsection
