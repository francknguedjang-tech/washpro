<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Première Connexion - WashPro</title>
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/font-inter/index.css') }}" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .card { border-radius: 20px; border: none; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
        .btn-primary { background-color: #4361ee; border: none; border-radius: 12px; padding: 12px; font-weight: 600; }
    </style>
</head>
<body class="d-flex align-items-center min-vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card p-4">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold">Sécurité</h2>
                        <p class="text-muted">Veuillez changer votre mot de passe pour votre première connexion.</p>
                    </div>
                    <form action="#" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-600">Nouveau mot de passe</label>
                            <input type="password" name="password" class="form-control rounded-3" required minlength="8">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-600">Confirmer le mot de passe</label>
                            <input type="password" name="password_confirmation" class="form-control rounded-3" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Changer le mot de passe</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>