{{-- Nom : auth/register.blade.php | Rôle : Formulaire d'inscription pour les nouveaux clients --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - WashPro</title>
    <!-- Bootstrap CSS -->
    <link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="{{ asset('vendor/font-poppins/index.css') }}" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}">

    <style>
        :root {
            --primary-color: #3b82f6; /* Premium Sky Blue */
            --secondary-color: #60a5fa; /* Lighter Sky Blue */
            --accent-color: #0ea5e9;
            --text-dark: #1e293b;
            --text-light: #64748b;
            --bg-light: #f0f9ff; /* Very soft sky blue background */
            --card-bg: rgba(255, 255, 255, 0.95);
            --input-bg: #ffffff;
            --border-color: #e2e8f0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            color: var(--text-dark);
            position: relative;
            overflow-x: hidden;
        }

        /* Animated background elements (Light Blue version) */
        body::before {
            content: '';
            position: absolute;
            top: -10%;
            left: -10%;
            width: 50%;
            height: 50%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.15) 0%, transparent 60%);
            border-radius: 50%;
            z-index: 0;
            filter: blur(60px);
        }

        body::after {
            content: '';
            position: absolute;
            bottom: -10%;
            right: -10%;
            width: 50%;
            height: 50%;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.15) 0%, transparent 60%);
            border-radius: 50%;
            z-index: 0;
            filter: blur(60px);
        }

        .auth-card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 20px 40px -10px rgba(59, 130, 246, 0.15), 0 0 0 1px rgba(255,255,255,0.5);
            width: 100%;
            max-width: 650px;
            overflow: hidden;
            z-index: 1;
            transform: translateY(0);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .auth-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px -12px rgba(59, 130, 246, 0.25), 0 0 0 1px rgba(255,255,255,0.8);
        }

        .auth-header {
            padding: 2.5rem 2.5rem 1.5rem 2.5rem;
            text-align: center;
            border-bottom: 1px solid var(--border-color);
        }

        .auth-header h2 {
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
        }

        .auth-header p {
            color: var(--text-light);
            font-weight: 500;
        }

        .auth-body {
            padding: 2rem 2.5rem 2.5rem 2.5rem;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text-dark);
            margin-bottom: 0.4rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .form-control {
            background-color: var(--input-bg);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            border: 2px solid var(--border-color);
            font-size: 0.95rem;
            color: var(--text-dark);
            transition: all 0.2s;
        }

        .form-control::placeholder {
            color: #cbd5e1;
        }

        .form-control:focus {
            background-color: white;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
            color: var(--text-dark);
        }

        .input-group-text {
            background-color: #f8fafc;
            border: 2px solid var(--border-color);
            border-right: none;
            border-radius: 12px 0 0 12px;
            color: var(--primary-color);
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 12px 12px 0;
        }

        .btn-auth {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 12px;
            padding: 0.85rem;
            font-weight: 600;
            color: white;
            width: 100%;
            margin-top: 1.5rem;
            transition: all 0.3s;
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.25);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(59, 130, 246, 0.35);
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            color: white;
        }

        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }

        .auth-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }
        
        .auth-footer a:hover {
            color: var(--secondary-color);
        }

        .auth-logo {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .auth-logo i {
            font-size: 2.2rem;
            color: var(--primary-color);
        }
    </style>
</head>
<body>

<div class="auth-card position-relative">
    <a href="{{ route('home') }}" class="position-absolute top-0 start-0 m-4 text-decoration-none z-3" title="Retour à l'accueil" style="color: var(--text-light); transition: all 0.3s;" onmouseover="this.style.color='var(--primary-color)'; this.style.transform='translateX(-3px)';" onmouseout="this.style.color='var(--text-light)'; this.style.transform='none';">
        <i class="bi bi-arrow-left-circle fs-3 shadow-sm bg-white rounded-circle"></i>
    </a>
    <div class="auth-header pt-5">
        <div class="auth-logo">
            <img src="{{ asset('img/logo.svg') }}" alt="WashPro Logo" style="height: 48px; width: auto;" class="mb-3">
        </div>
        <h2>Créer un compte</h2>
        <p class="mb-0 opacity-75">Rejoignez-nous et simplifiez votre quotidien.</p>
    </div>

    {{-- Formulaire principal divisé en sections (Identité, Contact, Sécurité) --}}
    <div class="auth-body">
        <form action="{{ route('register.post') }}" method="POST">
            @csrf

            @if($errors->any())
                <div class="alert alert-danger border-0 rounded-3 small mb-4">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Prénom</label>
                    <input type="text" name="prenom" class="form-control" value="{{ old('prenom') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nom</label>
                    <input type="text" name="nom" class="form-control" value="{{ old('nom') }}" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Numéro de téléphone</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                        <input type="text" name="telephone" class="form-control" value="{{ old('telephone') }}" placeholder="Ex: 0102030405" required>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Adresse Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="Ex: jean@example.com" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Mot de passe</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="Min. 8 caractères" required>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <label class="form-label">Confirmer mot de passe</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Répéter le mdp" required>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-auth">
                <i class="bi bi-person-plus-fill me-2"></i> Créer mon compte
            </button>

            <div class="auth-footer">
                <p class="small text-muted mb-0">
                    Vous avez déjà un compte ? <a href="{{ route('login') }}">Connectez-vous</a>
                </p>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap Bundle JS Local -->
<script src="{{ asset('bootstrap/js/bootstrap.bundle.min.js') }}"></script>

</body>
</html>