{{-- Nom : auth/login.blade.php | Rôle : Page de connexion pour tous les utilisateurs (Admin, Employés, Clients) --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Pressing Pro</title>
    <!-- Bootstrap CSS -->
    <link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="{{ asset('vendor/font-poppins/index.css') }}" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}">

    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4cc9f0;
            --text-dark: #2b2d42;
            --text-light: #8d99ae;
            --bg-light: #f8f9fa;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light);
            height: 100vh;
            overflow: hidden;
        }

        /* Layout Split Screen */
        .login-container {
            height: 100vh;
            width: 100%;
        }

        .left-side {
            background: linear-gradient(135deg, rgba(67, 97, 238, 0.9), rgba(63, 55, 201, 0.8)), url('{{ asset("img/login-bg.jpg") }}');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            padding: 2rem;
            position: relative;
        }

        .right-side {
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
        }

        /* Form Styling */
        .login-form {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
        }

        .brand-logo {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            display: block;
        }

        .login-title {
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .login-subtitle {
            color: var(--text-light);
            margin-bottom: 2rem;
            font-size: 0.95rem;
        }

        .form-floating > .form-control {
            border-radius: 12px;
            border: 2px solid #e9ecef;
            padding-left: 1rem;
            height: calc(3.5rem + 2px);
            font-size: 0.95rem;
        }

        .form-floating > .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.15);
        }

        .form-floating > label {
            padding-left: 1rem;
            color: var(--text-light);
        }

        .input-group-text {
            border-radius: 12px;
            border: 2px solid #e9ecef;
            background: transparent;
            color: var(--text-light);
            border-left: 0;
        }
        
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-login {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 12px;
            padding: 0.8rem;
            font-weight: 600;
            font-size: 1rem;
            letter-spacing: 0.5px;
            box-shadow: 0 10px 20px -10px rgba(67, 97, 238, 0.5);
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px -10px rgba(67, 97, 238, 0.6);
            background: linear-gradient(45deg, var(--secondary-color), var(--primary-color));
        }

        .custom-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: color 0.2s;
        }
        
        .custom-link:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }

        /* Illustration / Left Side Content */
        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            max-width: 450px;
            text-align: center;
        }

        /* Decorative Circles */
        .circle {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent-color), var(--primary-color));
            opacity: 0.1;
            z-index: 0;
        }
        .circle-1 { width: 150px; height: 150px; top: -50px; right: -50px; }
        .circle-2 { width: 100px; height: 100px; bottom: 50px; left: -30px; }

        @media (max-width: 768px) {
            .left-side {
                display: none !important;
            }
            .right-side {
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            }
            .login-card-mobile {
                background: white;
                padding: 2rem;
                border-radius: 20px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.05);
                width: 100%;
            }
            .login-form {
                padding: 0;
            }
        }
    </style>
</head>
<body>

<div class="row g-0 login-container">
    
    {{-- Côté gauche : Image de marque et Message de bienvenue --}}
    <div class="col-md-6 col-lg-7 d-none d-md-flex left-side">
        <div class="glass-card">
            <img src="{{ asset('img/logo.svg') }}" alt="WashPro Logo" style="height: 60px; width: auto;" class="mb-4">
            <p class="lead mb-4">Gérez votre activité de pressing avec élégance et efficacité.</p>
            <div class="d-flex gap-3 justify-content-center">
                <div class="text-center">
                    <i class="bi bi-shield-check fs-2 mb-2"></i>
                    <p class="small text-white-50">Sécurisé</p>
                </div>
                <div class="text-center mx-4">
                    <i class="bi bi-speedometer2 fs-2 mb-2"></i>
                    <p class="small text-white-50">Rapide</p>
                </div>
                <div class="text-center">
                    <i class="bi bi-graph-up-arrow fs-2 mb-2"></i>
                    <p class="small text-white-50">Efficace</p>
                </div>
            </div>
        </div>
        <p class="position-absolute bottom-0 mb-4 text-white-50 small">© 2026 WashPro. La qualité du service.</p>
    </div>

    {{-- Côté droit : Formulaire de connexion sécurisé --}}
    <div class="col-md-6 col-lg-5 right-side">
        <!-- Bouton Retour Accueil -->
        <a href="{{ route('home') }}" class="position-absolute top-0 start-0 m-4 text-decoration-none z-3" title="Retour à l'accueil" style="color: var(--text-light); transition: all 0.3s;" onmouseover="this.style.color='var(--primary-color)'; this.style.transform='translateX(-3px)';" onmouseout="this.style.color='var(--text-light)'; this.style.transform='none';">
            <i class="bi bi-arrow-left-circle fs-3 shadow-sm bg-white rounded-circle"></i>
        </a>

        <div class="circle circle-1"></div>
        <div class="circle circle-2"></div>

        <div class="login-form position-relative z-1">
            <div class="d-md-none text-center mb-4">
                <img src="{{ asset('img/logo.svg') }}" alt="WashPro Logo" style="height: 55px; width: auto;" class="mb-2"><p class="text-muted small">Votre gestionnaire de confiance</p>
            </div>

            <div class="login-card-mobile">
                <div class="mb-4">
                    <h2 class="login-title">Bienvenue ! </h2>
                    <p class="login-subtitle">Connectez-vous pour accéder à votre tableau de bord.</p>
                </div>

                <form action="{{ route('login') }}" method="POST">
                    @csrf
                   
                    @if($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4 small">
                            <i class="bi bi-exclamation-circle me-2"></i>
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="emailInput" name="email" placeholder="name@example.com" required>
                        <label for="emailInput"><i class="bi bi-envelope me-2"></i>Email ou Login</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="passwordInput" name="password" placeholder="Password" required>
                        <label for="passwordInput"><i class="bi bi-lock me-2"></i>Mot de passe</label>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="rememberMe">
                            <label class="form-check-label text-secondary small" for="rememberMe">
                                Se souvenir
                            </label>
                        </div>
                        <a href="{{ route('password.request') }}" class="custom-link small">Mot de passe oublié ?</a>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-login text-white mb-3">
                        Se connecter 
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                        </svg>
                    </button>

                    <p class="text-center text-muted small mt-4">
                        Vous n'avez pas de compte ? <a href="{{ route('register') }}" class="custom-link fw-bold">Inscrivez-vous ici</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

</div>

<!-- Bootstrap Bundle JS Local -->
<script src="{{ asset('bootstrap/js/bootstrap.bundle.min.js') }}"></script>

</body>
</html>