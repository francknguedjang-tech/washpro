<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres - Washpro</title>
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

        .form-control {
            border-radius: 12px;
            padding: 0.8rem 1rem;
            border: 1px solid #e2e8f0;
            font-weight: 500;
            transition: all 0.2s;
        }

        .form-control:focus {
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            border-color: var(--primary);
        }

        .form-label {
            font-weight: 600;
            color: var(--dark-light);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            border-radius: 12px;
            padding: 0.8rem 2rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
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
        
        <div class="d-flex align-items-center mb-4">
            <a href="{{ route('client.dashboard') }}" class="btn btn-sm btn-light border me-3 rounded-circle" style="width: 36px; height: 36px; display: inline-flex; justify-content: center; align-items: center;">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h2 class="fw-bold mb-0">Paramètres du compte</h2>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center">
                <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                <div>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="panel-card">
                    <div class="panel-header">
                        <h4 class="panel-title"><i class="bi bi-person-badge text-primary me-2"></i> Informations Personnelles</h4>
                        <span class="badge bg-light text-dark border"><i class="bi bi-shield-check text-success"></i> Sécurisé</span>
                    </div>
                    <div class="p-4 p-md-5">
                        <form action="{{ route('client.settings.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Prénom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="prenom" value="{{ old('prenom', $user->prenom) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="nom" value="{{ old('nom', $user->nom) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Téléphone <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="telephone" value="{{ old('telephone', $user->telephone) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" value="{{ old('email', $user->email) }}">
                                    <div class="form-text mt-1 text-muted"><i class="bi bi-info-circle"></i> Nécessaire pour recevoir vos reçus par email.</div>
                                </div>
                            </div>

                            <hr class="border-light my-5">

                            <h5 class="fw-bold mb-4"><i class="bi bi-key text-secondary me-2"></i> Modifier le mot de passe</h5>
                            <p class="text-muted small mb-4">Laissez ces champs vides si vous ne souhaitez pas modifier votre mot de passe pour le moment.</p>

                            <div class="row g-4 mb-4">
                                <div class="col-md-12">
                                    <label class="form-label">Mot de passe actuel</label>
                                    <input type="password" class="form-control" name="current_password">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nouveau mot de passe</label>
                                    <input type="password" class="form-control" name="new_password">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Confirmer le nouveau mot de passe</label>
                                    <input type="password" class="form-control" name="new_password_confirmation">
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-5">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-floppy me-2"></i> Enregistrer les modifications
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    
</body>
</html>
