{{-- Nom : client/dashboard.blade.php | Rôle : Espace personnel du client (Consultation de l'état des linges et facture) --}}
<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Espace Client - Washpro</title>
    <!-- Google Fonts -->
    <link href="{{ asset('vendor/font-plus-jakarta-sans/index.css') }}" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}">
    <!-- Chart.js -->
    <script src="{{ asset('vendor/chartjs/chart.umd.js') }}"></script>

    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --secondary: #38bdf8;
            --accent: #f43f5e;
            --dark: #0f172a;
            --dark-light: #1e293b;
            --light: #eff6ff; /* More blue tint */
            --glass: rgba(255, 255, 255, 0.95); /* More solid professional look for navbar */
            --card-shadow: 0 10px 30px rgba(0,0,0,0.03);
            --font-main: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            font-family: var(--font-main);
            color: var(--dark);
            background-color: var(--light);
            -webkit-font-smoothing: antialiased;
            padding-top: 80px;
        }

        .text-gradient {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        }

        {{-- Barre de navigation supérieure --}}
        .navbar-top {
            background: #ffffff;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
            z-index: 1030;
        }

        .navbar-brand {
            font-weight: 800;
            color: var(--primary) !important;
        }

        /* Layout */
        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .welcome-section {
            background: white;
            border-radius: 24px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--card-shadow);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.03);
        }

        .welcome-section::after {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.2) 0%, transparent 70%);
            border-radius: 50%;
        }

        /* Stats Cards */
        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            border: 1px solid rgba(0,0,0,0.03);
            box-shadow: var(--card-shadow);
            height: 100%;
            display: flex;
            align-items: center;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-right: 1.2rem;
            flex-shrink: 0;
        }

        .stat-content h3 {
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 0.2rem;
            color: var(--dark);
        }

        .stat-content p {
            color: #64748b;
            font-weight: 500;
            margin-bottom: 0;
            font-size: 0.9rem;
        }

        }

        {{-- Cartes de statistiques et contenu --}}
        .panel-card {
            background: white;
            border-radius: 24px;
            border: 1px solid rgba(0,0,0,0.03);
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .panel-header {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid #f1f5f9;
            background: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .panel-title {
            font-weight: 800;
            font-size: 1.2rem;
            margin-bottom: 0;
            color: var(--dark);
        }

        /* Table Styles */
        .table-responsive {
            padding: 0 2rem;
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            font-weight: 700;
            color: #64748b;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            border-bottom: 2px solid #e2e8f0;
            padding: 1.2rem 1rem;
            background: #f8fafc;
        }

        .table td {
            vertical-align: middle;
            padding: 1.5rem 1rem;
            color: var(--dark-light);
            border-bottom: 1px solid #f1f5f9;
            transition: background-color 0.2s ease;
        }
        
        .table-hover tbody tr {
            transition: all 0.2s ease;
        }
        
        .table-hover tbody tr:hover {
            background-color: #ffffff !important;
            box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        }

        /* Badges */
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .status-en_cours { background: rgba(245, 158, 11, 0.1); color: #d97706; }
        .status-pret { background: rgba(16, 185, 129, 0.1); color: #059669; }
        .status-recuperer { background: rgba(37, 99, 235, 0.1); color: #2563eb; }
        .status-default { background: rgba(100, 116, 139, 0.1); color: #475569; }

        .btn-action {
            padding: 0.4rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.2s;
        }

        .btn-outline-primary-soft {
            color: var(--primary);
            background: rgba(37, 99, 235, 0.05);
            border: 1px solid transparent;
        }

        .btn-outline-primary-soft:hover {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary-dark);
        }

        /* Nav Pills Custom */
        .custom-tabs .nav-link {
            color: #64748b;
            font-weight: 600;
            border-radius: 12px;
            padding: 0.6rem 1.5rem;
            margin-right: 0.5rem;
            transition: all 0.3s;
        }

        .custom-tabs .nav-link:hover {
            background: #f1f5f9;
        }

        .custom-tabs .nav-link.active {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
        }

        /* Chart container */
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
            padding: 1rem;
        }

        /* Modal custom */
        .modal-content {
            border-radius: 24px;
            border: none;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
    </style>
</head>
<body>

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top navbar-top">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                <img src="{{ asset('img/logo.svg') }}" alt="WashPro Logo" style="height: 32px; width: auto;">
            </a>

            <div class="d-flex align-items-center gap-4">
                
                <!-- Notification Bell -->
                <div class="dropdown">
                    <button class="btn border-0 p-0 text-dark position-relative" type="button" data-bs-toggle="dropdown" title="Notifications">
                        <i class="bi bi-bell fs-5"></i>
                        @if(isset($notifications) && $notifications->count() > 0)
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                                <span class="visually-hidden">Nouvelles notifications</span>
                            </span>
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 mt-3 p-0" style="width: 320px; max-height: 400px; overflow-y: auto;">
                        <li class="p-3 border-bottom d-flex justify-content-between align-items-center sticky-top bg-white rounded-top-4">
                            <h6 class="mb-0 fw-bold">Notifications</h6>
                            @if(isset($notifications) && $notifications->count() > 0)
                                <span class="badge bg-primary rounded-pill">{{ $notifications->count() }}</span>
                            @endif
                        </li>
                        @if(isset($notifications) && $notifications->count() > 0)
                            @foreach($notifications as $notif)
                                <li>
                                    <a class="dropdown-item p-3 border-bottom text-wrap" href="#">
                                        <div class="d-flex gap-2">
                                            <div class="text-primary mt-1"><i class="bi bi-info-circle-fill"></i></div>
                                            <div>
                                                <p class="mb-1 small fw-medium">{{ $notif->message }}</p>
                                                <small class="text-muted" style="font-size: 0.75rem;">{{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                            <li><a href="#" class="dropdown-item text-center p-2 text-primary fw-bold small">Voir tout</a></li>
                        @else
                            <li><div class="p-4 text-center text-muted small">Aucune nouvelle notification</div></li>
                        @endif
                    </ul>
                </div>

                <!-- User Profile -->
                <div class="dropdown">
                    <button class="btn border-0 p-0 text-dark fw-bold d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                        <div class="d-flex align-items-center justify-content-center bg-dark text-white rounded-circle fw-bold" style="width: 42px; height: 42px; font-size: 1.1rem;">
                            {{ substr(Auth::user()->prenom, 0, 1) }}
                        </div>
                        <div class="d-none d-sm-flex flex-column align-items-start text-start ms-1">
                            <span class="fw-bold text-dark lh-1" style="font-size: 0.95rem;">
                                {{ Auth::user()->prenom }} <span class="badge bg-light text-dark border ms-1" style="font-size: 0.65rem;">{{ strtoupper(Auth::user()->role ?? 'STANDARD') }}</span>
                            </span>
                            <span class="text-muted lh-1 mt-1" style="font-size: 0.8rem;">Compte {{ ucfirst(Auth::user()->role ?? 'Actif') }}</span>
                        </div>
                        <i class="bi bi-chevron-down small text-muted ms-2 mt-1"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 mt-3 p-2" style="min-width: 220px;">
                        <li>
                            <div class="px-3 py-2">
                                <span class="fw-bold d-block text-dark">{{ Auth::user()->prenom }} {{ Auth::user()->nom }}</span>
                                <small class="text-muted">{{ Auth::user()->telephone ?? Auth::user()->email }}</small>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item rounded-3 d-flex align-items-center gap-2 py-2 fw-medium" href="{{ route('client.settings') }}">
                                <i class="bi bi-gear text-secondary"></i> Paramètres
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item rounded-3 d-flex align-items-center gap-2 py-2 fw-medium" href="{{ route('client.help') }}">
                                <i class="bi bi-question-circle text-primary"></i> Aide
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="dropdown-item rounded-3 text-danger fw-semibold d-flex align-items-center gap-2 py-2" type="submit">
                                    <i class="bi bi-box-arrow-right"></i> Déconnexion
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="dashboard-container">
        
        <!-- Welcome Area -->
        <div class="welcome-section">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="fw-bold mb-2">Bonjour, <span class="text-primary">{{ Auth::user()->prenom }}</span> ! </h2>
                    <p class="text-muted mb-0 fs-5">Voici un aperçu de vos activités récentes et de vos dépôts.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <button class="btn btn-primary rounded-pill px-4 py-2 fw-bold" onclick="window.scrollTo(0, document.body.scrollHeight);">Voir mes paiements</button>
                </div>
            </div>
        </div>

        <!-- Global Stats -->
        <div class="row g-4 mb-5">
            <div class="col-sm-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-bag-check-fill"></i>
                    </div>
                    <div class="stat-content">
                        <h3>{{ $stats['total_depots'] }}</h3>
                        <p>Total Dépôts</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div class="stat-content">
                        <h3>{{ $stats['en_cours'] }}</h3>
                        <p>En Traitement</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-box2-heart-fill"></i>
                    </div>
                    <div class="stat-content">
                        <h3>{{ $stats['pret'] }}</h3>
                        <p>Prêt à retirer</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(244, 63, 94, 0.1); color: #f43f5e;">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div class="stat-content">
                        <h3>{{ number_format($stats['total_depense'], 0, ',', ' ') }} <small class="fs-6 text-muted fw-normal">FCFA</small></h3>
                        <p>Dépense Totale</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <!-- Table Dépôts -->
            <div class="col-xl-8">
                <div class="panel-card h-100">
                    <div class="panel-header">
                        <h4 class="panel-title"><i class="bi bi-basket2 text-primary me-2"></i> Historique de mes dépôts</h4>
                    </div>
                    <div class="table-responsive py-3">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Date</th>
                                    <th>Articles / Détails</th>
                                    <th>Statut</th>
                                    <th class="text-end">Montant Total</th>
                                    <th class="text-end">Reste à payer</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($depots as $index => $depot)
                                    <tr class="{{ $index >= 5 ? 'd-none hidden-depot-row' : '' }}">
                                        <td class="fw-bold">#{{ str_pad($depot->id, 5, '0', STR_PAD_LEFT) }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $depot->created_at->format('d M. Y') }}</div>
                                            <span class="text-muted small">Prévu le {{ \Carbon\Carbon::parse($depot->date_retrait_prevue)->format('d/m') }}</span>
                                        </td>
                                        <td>
                                            @foreach($depot->linges->take(2) as $linge)
                                                <div class="small fw-semibold text-dark">{{ $linge->quantite }}x {{ $linge->service->libelle }}</div>
                                            @endforeach
                                            @if($depot->linges->count() > 2)
                                                <div class="small text-muted">+ {{ $depot->linges->count() - 2 }} autre(s) article(s)</div>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = match(strtolower($depot->etat)) {
                                                    'en cours', 'en attente', 'en traitement' => 'status-en_cours',
                                                    'pret' => 'status-pret',
                                                    'recuperer', 'livré' => 'status-recuperer',
                                                    default => 'status-default'
                                                };
                                                $iconClass = match(strtolower($depot->etat)) {
                                                    'en cours', 'en attente', 'en traitement' => 'bi-arrow-repeat',
                                                    'pret' => 'bi-check-circle-fill',
                                                    'recuperer', 'livré' => 'bi-check-all',
                                                    default => 'bi-info-circle'
                                                };
                                            @endphp
                                            <span class="status-badge {{ $statusClass }}">
                                                <i class="bi {{ $iconClass }}"></i> 
                                                {{ ucfirst($depot->etat == 'recuperer' ? 'récupéré' : $depot->etat) }}
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold text-dark fs-6">
                                            {{ number_format($depot->prix_total, 0, ',', ' ') }} F
                                        </td>
                                        <td class="text-end fw-bold">
                                            @if($depot->reste_a_payer == 0)
                                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 shadow-sm border border-success border-opacity-25" style="letter-spacing: 0.5px;"><i class="bi bi-check-circle-fill me-1"></i> Payé</span>
                                            @else
                                                <div class="d-flex flex-column align-items-end gap-2">
                                                    <span class="text-danger bg-danger bg-opacity-10 rounded-pill px-3 py-2 d-inline-flex align-items-center shadow-sm border border-danger border-opacity-25 mb-1" style="letter-spacing: 0.5px;"><i class="bi bi-exclamation-circle-fill me-1"></i> {{ number_format($depot->reste_a_payer, 0, ',', ' ') }} F</span>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-primary rounded-pill fw-bold shadow-sm" 
                                                            style="font-size: 0.75rem;"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#paymentModal" 
                                                            data-depot-id="{{ $depot->id }}" 
                                                            data-reste="{{ $depot->reste_a_payer }}">
                                                        <i class="bi bi-credit-card-fill me-1"></i> Payer en ligne
                                                    </button>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <i class="bi bi-inbox fs-1 text-muted mb-3 d-block"></i>
                                            <h5 class="fw-bold text-dark">Aucun dépôt</h5>
                                            <p class="text-muted">Vous n'avez pas encore confié de linge à notre pressing.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($depots->count() > 5)
                        <div class="text-center pb-4 pt-2 border-top">
                            <button class="btn btn-outline-primary btn-sm rounded-pill px-4" onclick="document.querySelectorAll('.hidden-depot-row').forEach(row => row.classList.remove('d-none')); this.style.display='none';">
                                Voir la suite de l'historique ({{ $depots->count() - 5 }})
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Chart Activity -->
            <div class="col-xl-4">
                <div class="panel-card h-100">
                    <div class="panel-header">
                        <h4 class="panel-title"><i class="bi bi-bar-chart-fill text-secondary me-2"></i> Ma Fréquence</h4>
                    </div>
                    <div class="chart-container">
                        <canvas id="usageChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Paiements & Reçus -->
        <div class="row">
            <div class="col-12">
                <div class="panel-card">
                    <div class="panel-header">
                        <h4 class="panel-title"><i class="bi bi-receipt text-success me-2"></i> Historique des Paiements et Reçus</h4>
                    </div>
                    <div class="table-responsive py-3">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Montant Payé</th>
                                    <th>Mode de Paiement</th>
                                    <th>Lié au Dépôt</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paiements as $index => $paiement)
                                    <tr class="{{ $index >= 5 ? 'd-none hidden-paiement-row' : '' }}">
                                        <td class="fw-semibold">{{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d M. Y') }}</td>
                                        <td class="text-success fw-bold">+ {{ number_format($paiement->montant, 0, ',', ' ') }} F</td>
                                        <td>
                                            @if($paiement->mode_paiement == 'orange_money')
                                                <span class="badge bg-warning text-dark"><i class="bi bi-phone"></i> Orange Money</span>
                                            @elseif($paiement->mode_paiement == 'mobile_money')
                                                <span class="badge bg-dark"><i class="bi bi-phone"></i> Mobile Money</span>
                                            @else
                                                <span class="badge bg-success bg-opacity-10 text-success"><i class="bi bi-cash"></i> Espèces</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-muted fw-bold">#{{ str_pad($paiement->depot_id, 5, '0', STR_PAD_LEFT) }}</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('client.depots.recu', $paiement->depot_id) }}" class="btn btn-action btn-outline-primary-soft" target="_blank">
                                                <i class="bi bi-download me-1"></i> Mon Reçu
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <p class="text-muted mb-0">Aucun historique de paiement disponible.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($paiements->count() > 5)
                        <div class="text-center pb-4 pt-2 border-top">
                            <button class="btn btn-outline-success btn-sm rounded-pill px-4" onclick="document.querySelectorAll('.hidden-paiement-row').forEach(row => row.classList.remove('d-none')); this.style.display='none';">
                                Voir la suite de l'historique ({{ $paiements->count() - 5 }})
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    <!-- Modale de Paiement Flexible -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white border-0 py-3">
                    <h5 class="modal-title fw-bold" id="paymentModalLabel"><i class="bi bi-credit-card me-2"></i> Régler mon dépôt</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="paymentForm" method="POST" action="">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Montant à verser (FCFA)</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-currency-exchange text-primary"></i></span>
                                <input type="number" name="montant" id="paymentAmount" class="form-control bg-light border-start-0 fw-bold" placeholder="Ex: 2000" min="100" required>
                            </div>
                            <div class="form-text mt-2">
                                Reste à payer : <span id="resteText" class="fw-bold text-primary">0</span> F
                            </div>
                        </div>
                        <div class="alert alert-info border-0 rounded-4 small mb-0">
                            <i class="bi bi-info-circle-fill me-2"></i> Vous pouvez payer la totalité ou une partie de votre facture en ligne.
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Confirmer le paiement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Script pour gérer la modale de paiement
            const paymentModal = document.getElementById('paymentModal');
            if (paymentModal) {
                paymentModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const depotId = button.getAttribute('data-depot-id');
                    const reste = button.getAttribute('data-reste');
                    
                    const form = paymentModal.querySelector('#paymentForm');
                    const input = paymentModal.querySelector('#paymentAmount');
                    const resteText = paymentModal.querySelector('#resteText');
                    
                    // Mettre à jour l'action du formulaire avec l'ID du dépôt
                    form.action = `/client/paiement/${depotId}/initier`;
                    
                    // Configurer le montant max et la valeur par défaut
                    input.max = reste;
                    input.value = reste;
                    resteText.textContent = Number(reste).toLocaleString('fr-FR');
                });
            }

            const ctx = document.getElementById('usageChart').getContext('2d');
            
            const chartLabels = {!! json_encode($chartData['labels']) !!};
            const chartData = {!! json_encode($chartData['data']) !!};

            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(37, 99, 235, 1)'); 
            gradient.addColorStop(1, 'rgba(56, 189, 248, 0.1)'); 

            const hoverGradient = ctx.createLinearGradient(0, 0, 0, 400);
            hoverGradient.addColorStop(0, 'rgba(30, 64, 175, 1)');
            hoverGradient.addColorStop(1, 'rgba(37, 99, 235, 0.3)');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Dépôts du mois',
                        data: chartData,
                        backgroundColor: gradient,
                        borderRadius: {topLeft: 12, topRight: 12, bottomLeft: 2, bottomRight: 2},
                        borderSkipped: false,
                        barThickness: 28,
                        hoverBackgroundColor: hoverGradient,
                        hoverBorderWidth: 2,
                        hoverBorderColor: 'rgba(37, 99, 235, 1)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        y: {
                            duration: 2000,
                            easing: 'easeOutElastic'
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            padding: 12,
                            titleFont: { size: 14, family: "'Plus Jakarta Sans', sans-serif" },
                            bodyFont: { size: 13, family: "'Plus Jakarta Sans', sans-serif" }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                color: '#64748b'
                            },
                            grid: {
                                color: '#f1f5f9',
                                drawBorder: false
                            }
                        },
                        x: {
                            ticks: {
                                color: '#64748b'
                            },
                            grid: {
                                display: false,
                                drawBorder: false
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
