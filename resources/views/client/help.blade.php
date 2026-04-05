<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centre d'aide - Washpro</title>
    <!-- Google Fonts -->
    <link href="{{ asset('vendor/font-plus-jakarta-sans/index.css') }}" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}">

    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --secondary: #38bdf8;
            --accent: #f43f5e;
            --dark: #0f172a;
            --dark-light: #1e293b;
            --light: #eff6ff; 
            --glass: rgba(255, 255, 255, 0.95);
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

        /* Navbar */
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
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Cards Panels */
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

        /* Accordion Custom */
        .accordion-item {
            border: none;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 0.5rem;
        }
        
        .accordion-item:last-child {
            border-bottom: none;
        }

        .accordion-button {
            font-weight: 700;
            color: var(--dark);
            background-color: transparent !important;
            padding: 1.5rem 1rem;
            box-shadow: none !important;
        }
        
        .accordion-button:not(.collapsed) {
            color: var(--primary);
        }

        .accordion-body {
            color: #64748b;
            line-height: 1.7;
            padding: 0 1rem 1.5rem 1rem;
        }

        /* Contact Cards */
        .contact-box {
            background: #f8fafc;
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            border: 1px solid #e2e8f0;
            transition: transform 0.2s;
            height: 100%;
        }

        .contact-box:hover {
            transform: translateY(-5px);
            border-color: var(--secondary);
            background: white;
            box-shadow: var(--card-shadow);
        }

        .contact-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin: 0 auto 1rem auto;
        }
    </style>
</head>
<body>

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top navbar-top">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('client.dashboard') }}">
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
                            <a class="dropdown-item rounded-3 d-flex align-items-center gap-2 py-2 fw-medium" href="{{ route('client.dashboard') }}">
                                <i class="bi bi-grid text-secondary"></i> Mon Dashboard
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item rounded-3 d-flex align-items-center gap-2 py-2 fw-medium" href="{{ route('client.settings') }}">
                                <i class="bi bi-gear text-secondary"></i> Paramètres
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
        
        <div class="d-flex align-items-center mb-5">
            <a href="{{ route('client.dashboard') }}" class="btn btn-sm btn-light border me-3 rounded-circle" style="width: 36px; height: 36px; display: inline-flex; justify-content: center; align-items: center;">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h2 class="fw-bold mb-0">Centre d'aide en ligne</h2>
                <p class="text-muted mb-0 mt-1">Trouvez rapidement les réponses à vos questions.</p>
            </div>
        </div>

        <div class="row g-4">
            <!-- FAQ Section -->
            <div class="col-lg-8">
                <div class="panel-card p-4 p-md-5">
                    <h4 class="fw-bold mb-4"><i class="bi bi-question-circle text-primary me-2"></i> Questions Fréquentes (FAQ)</h4>
                    
                    <div class="accordion" id="faqAccordion">
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                    Comment suivre l'état de mon linge ?
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Vous pouvez suivre l'état de votre linge directement depuis votre <strong>Dashboard</strong> principal. Dans la liste de vos récents dépôts, la colonne "Statut" vous indique si votre linge est "En cours", "En traitement", ou "Prêt". De plus, vous recevrez une notification et un email dès que vos vêtements seront prêts à être retirés.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                    Comment télécharger mon reçu de paiement ?
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Rendez-vous sur votre Dashboard. En bas de page, dans la section <strong>Historique des Paiements et Reçus</strong>, vous trouverez la liste complète de tous les paiements effectués. Il vous suffit de cliquer sur le bouton bleu "Mon Reçu" situé complètement à droite de la ligne correspondante pour générer et télécharger votre reçu au format PDF ou Impression.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                    Quels sont les modes de paiement acceptés ?
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Nous acceptons plusieurs modes de paiement pour votre confort :
                                    <ul class="mt-2 text-dark fw-medium">
                                        <li>Paiement en espèces (Cache)</li>
                                        <li>Orange Money</li>
                                        <li>Mobile Money (MTN, Moov, etc.)</li>
                                    </ul>
                                    Les paiements peuvent s'effectuer en avance (partiel ou total) ou à la récupération.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour">
                                    Mes données personnelles sont-elles sécurisées ?
                                </button>
                            </h2>
                            <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Oui, 100%. Vos données personnelles (téléphone, email, nom) sont stockées de matière cryptée et ne sont utilisées que dans le cadre exclusif de notre service (envoi de reçus, notifications pour venir récupérer votre linge).
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Contact Section -->
            <div class="col-lg-4">
                <div class="panel-card p-4 border-top-0 border-start-0 border-end-0 border-bottom border-primary border-4">
                    <h5 class="fw-bold mb-4 text-center">Vous ne trouvez pas de réponse ?</h5>
                    <p class="text-center text-muted small mb-4">Notre service client est disponible de 8h à 19h pour vous assister.</p>
                    
                    <div class="contact-box mb-3">
                        <div class="contact-icon d-flex">
                            <i class="bi bi-telephone-fill"></i>
                        </div>
                        <h6 class="fw-bold text-dark">Appelez-nous</h6>
                        <p class="text-muted small mb-0">+237 698 25 57 25</p> <!-- Numéro d'assistance WashPro -->
                    </div>

                    <div class="contact-box">
                        <div class="contact-icon d-flex" style="color: #25d366; background: rgba(37, 211, 102, 0.1);">
                            <i class="bi bi-whatsapp"></i>
                        </div>
                        <h6 class="fw-bold text-dark">WhatsApp</h6>
                        <p class="text-muted small mb-0">Assistance rapide</p>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    
</body>
</html>
