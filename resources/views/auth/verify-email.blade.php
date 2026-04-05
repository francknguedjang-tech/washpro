<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification de l'E-mail - WashPro</title>
    <!-- Bootstrap CSS -->
    <link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="{{ asset('vendor/font-poppins/index.css') }}" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}">

    <style>
        :root {
            --primary-color: #3b82f6;
            --secondary-color: #60a5fa;
            --text-dark: #1e293b;
            --bg-light: #f0f9ff;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .verify-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 20px 40px -10px rgba(59, 130, 246, 0.15);
            width: 100%;
            max-width: 450px;
            padding: 3rem 2.5rem;
            text-align: center;
        }

        .icon-circle {
            width: 80px;
            height: 80px;
            background-color: rgba(59, 130, 246, 0.1);
            color: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 1.5rem auto;
        }

        .auth-logo {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .code-input-container {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 2rem 0;
            direction: ltr;
        }

        .code-digit {
            width: 50px;
            height: 60px;
            font-size: 1.5rem;
            font-weight: 700;
            text-align: center;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            background-color: #ffffff;
            color: var(--text-dark);
            transition: all 0.2s;
        }

        .code-digit:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        .btn-verify {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 12px;
            padding: 0.85rem;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s;
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.25);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .btn-verify:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(59, 130, 246, 0.35);
            color: white;
        }
    </style>
</head>
<body>

<div class="verify-card">
    <div class="auth-logo">
        <img src="{{ asset('img/logo.svg') }}" alt="WashPro Logo" style="height: 48px; width: auto;" class="mb-3">
    </div>
    
    <div class="icon-circle">
        <i class="bi bi-envelope-check"></i>
    </div>

    <h3 class="fw-bold mb-3">Vérifiez votre E-mail</h3>
    <p class="text-muted small mb-4">
        Nous avons envoyé un code de vérification à 6 chiffres à votre adresse e-mail. Veuillez le saisir ci-dessous pour finaliser votre inscription.
    </p>

    @if(session('success'))
        <div class="alert alert-success shadow-sm border-0 rounded-3 small">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger shadow-sm border-0 rounded-3 small">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('verify.email.submit') }}" method="POST" id="verifyForm">
        @csrf
        <input type="hidden" name="verification_code" id="verification_code">
        
        <div class="code-input-container">
            <input type="text" class="code-digit" maxlength="1" autocomplete="off" autofocus>
            <input type="text" class="code-digit" maxlength="1" autocomplete="off">
            <input type="text" class="code-digit" maxlength="1" autocomplete="off">
            <input type="text" class="code-digit" maxlength="1" autocomplete="off">
            <input type="text" class="code-digit" maxlength="1" autocomplete="off">
            <input type="text" class="code-digit" maxlength="1" autocomplete="off">
        </div>

        <button type="submit" class="btn btn-verify mt-2">
            Confirmer le code
        </button>
    </form>
    
    <div class="mt-4 pt-3 border-top">
        <form action="{{ route('logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-link text-muted small text-decoration-none">
                <i class="bi bi-box-arrow-left me-1"></i> Se déconnecter pour l'instant
            </button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.code-digit');
        const hiddenInput = document.getElementById('verification_code');
        const form = document.getElementById('verifyForm');

        inputs.forEach((input, index) => {
            input.addEventListener('input', function(e) {
                // Ensure only numbers
                this.value = this.value.replace(/[^0-9]/g, '');
                
                if (this.value.length === 1) {
                    if (index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                }
                updateHiddenInput();
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && this.value === '') {
                    if (index > 0) {
                        inputs[index - 1].focus();
                    }
                }
            });

            // Handle paste
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text').replace(/[^0-9]/g, '').slice(0, 6);
                
                pastedData.split('').forEach((char, i) => {
                    if (i < inputs.length) {
                        inputs[i].value = char;
                    }
                });
                
                if (pastedData.length > 0) {
                    inputs[Math.min(pastedData.length, inputs.length) - 1].focus();
                }
                updateHiddenInput();
            });
        });

        function updateHiddenInput() {
            let code = '';
            inputs.forEach(input => code += input.value);
            hiddenInput.value = code;
        }

        form.addEventListener('submit', function(e) {
            updateHiddenInput();
            if (hiddenInput.value.length !== 6) {
                e.preventDefault();
                alert('Veuillez saisir les 6 chiffres du code.');
            }
        });
    });
</script>

<!-- Bootstrap Bundle JS Local -->
<script src="{{ asset('bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
