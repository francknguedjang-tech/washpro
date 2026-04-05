<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mise à jour de votre dépôt</title>
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
        .status-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
            text-align: center;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            margin-top: 15px;
            color: #ffffff;
            /* Default background (will be overridden) */
            background-color: #4361ee; 
        }
        
        /* Status Colors */
        .status-en-cours { background-color: #f59e0b; }
        .status-pret { background-color: #10b981; }
        .status-recuperer { background-color: #6366f1; }

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
            <p style="margin: 5px 0 0; opacity: 0.9;">Suivi de Dépôt</p>
        </div>
        <div class="content">
            <h2>Bonjour {{ $depot->client->prenom }},</h2>
            <p>Nous vous informons que le statut de votre dépôt <strong>{{ $depot->reference }}</strong> a été mis à jour.</p>
            
            <div class="status-box">
                <p style="margin: 0; color: #64748b; font-size: 15px;">Nouveau Statut :</p>
                
                @php
                    $statusClass = 'status-en-cours';
                    $statusText = 'EN COURS';
                    if ($depot->etat == 'pret') {
                        $statusClass = 'status-pret';
                        $statusText = 'PRÊT POUR RETRAIT';
                    } elseif ($depot->etat == 'recuperer') {
                        $statusClass = 'status-recuperer';
                        $statusText = 'TERMINÉ (RÉCUPÉRÉ)';
                    }
                @endphp

                <div class="status-badge {{ $statusClass }}">
                    {{ $statusText }}
                </div>
                
                @if($depot->etat == 'pret')
                    <p style="margin-top: 20px; font-weight: bold; color: #10b981;">Vos vêtements sont propres et prêts à être récupérés au pressing !</p>
                @endif
            </div>

            <p>Veuillez vous présenter muni(e) de votre ticket de dépôt (ou ce message) ainsi que du solde restant si applicable.</p>
            
            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ route('login') }}" class="btn">Consulter mon compte</a>
            </div>
        </div>
        <div class="footer">
            <p>À très bientôt chez WashPro.</p>
            <p>&copy; {{ date('Y') }} WashPro. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
