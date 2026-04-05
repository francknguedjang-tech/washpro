{{-- Nom : emails/client-created.blade.php | Rôle : Email de bienvenue envoyé aux nouveaux clients avec leurs identifiants --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue chez WashPro</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }
        .header {
            background: linear-gradient(135deg, #4361ee, #3f37c9);
            color: white;
            text-align: center;
            padding: 40px 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .content {
            padding: 40px 30px;
        }
        .content h2 {
            color: #111827;
            font-size: 22px;
            margin-top: 0;
        }
        .credentials-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
            text-align: center;
        }
        .credentials-box p {
            margin: 5px 0;
            color: #4b5563;
        }
        .highlight {
            font-size: 18px;
            font-weight: bold;
            color: #4361ee;
            background: #eff6ff;
            padding: 5px 15px;
            border-radius: 6px;
            display: inline-block;
            margin-top: 5px;
        }
        .btn {
            display: inline-block;
            background-color: #4361ee;
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 50px;
            font-weight: bold;
            font-size: 16px;
            text-align: center;
            margin-top: 15px;
            box-shadow: 0 4px 6px rgba(67, 97, 238, 0.2);
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px;
            text-align: center;
            font-size: 13px;
            color: #6b7280;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>WASHPRO</h1>
        </div>
        <div class="content">
            <h2>Bienvenue, {{ $client->prenom }} ! 🎉</h2>
            <p>Nous sommes ravis de vous compter parmi nos clients.</p>
            <p>Votre compte client a été créé avec succès par notre équipe. Vous pouvez dès à présent suivre l'état de vos dépôts, consulter votre historique de paiements et vos factures en vous connectant à notre plateforme web.</p>
            
            <div class="credentials-box">
                <p><strong>Vos identifiants de connexion :</strong></p>
                <p style="margin-top: 15px;">Téléphone (Identifiant) :</p>
                <div class="highlight">{{ $client->telephone }}</div>
                <p style="margin-top: 15px;">Mot de passe temporaire :</p>
                <div class="highlight">{{ $password }}</div>
            </div>

            <p style="text-align: center; font-size: 14px; color: #ef4444;">
                <em>⚠️ Lors de votre première connexion, il vous sera demandé de modifier ce mot de passe.</em>
            </p>

            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ route('login') }}" class="btn">Accéder à mon espace</a>
            </div>
        </div>
        <div class="footer">
            <p>Merci pour votre confiance !</p>
            <p>&copy; {{ date('Y') }} WashPro. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
