<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre code de vérification</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f6f9;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        .header {
            background-color: #3b82f6;
            color: #ffffff;
            text-align: center;
            padding: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
            text-align: center;
        }
        .code-box {
            display: inline-block;
            background-color: #f0f9ff;
            border: 2px dashed #3b82f6;
            color: #1d4ed8;
            font-size: 32px;
            font-weight: bold;
            padding: 15px 30px;
            letter-spacing: 5px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .footer {
            background-color: #f8fafc;
            color: #64748b;
            text-align: center;
            padding: 15px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Bienvenue sur WashPro</h1>
        </div>
        <div class="content">
            <h2>Vérification de votre adresse e-mail</h2>
            <p>Bonjour,</p>
            <p>Merci de vous être inscrit sur WashPro ! Pour finaliser la création de votre compte, veuillez saisir le code de vérification suivant :</p>
            
            <div class="code-box">
                {{ $code }}
            </div>

            <p>Ce code est valable pendant une courte durée. Ne le partagez avec personne.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} WashPro. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
