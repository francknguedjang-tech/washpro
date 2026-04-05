<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu de Paiement</title>
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
            background: linear-gradient(135deg, #10b981, #059669);
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
        .receipt-box {
            background-color: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
            box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05);
        }
        .receipt-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        .receipt-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .receipt-label {
            color: #64748b;
            font-weight: 600;
            font-size: 14px;
        }
        .receipt-value {
            color: #0f172a;
            font-weight: bold;
            font-size: 15px;
            text-align: right;
        }
        .amount-highlight {
            font-size: 24px;
            color: #059669;
            text-align: center;
            font-weight: 900;
            margin-top: 15px;
        }
        .badge-status {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
        }
        .status-paye { background-color: #d1fae5; color: #047857; }
        .status-partiel { background-color: #fef3c7; color: #b45309; }
        
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
            <p style="margin: 5px 0 0; opacity: 0.9;">Reçu de Versement Paiement</p>
        </div>
        <div class="content">
            <h2>Bonjour {{ $depot->client->prenom }},</h2>
            <p>Nous vous confirmons la bonne réception de votre paiement pour le dépôt <strong>{{ $depot->reference }}</strong>.</p>
            
            <div class="amount-highlight">
                +{{ number_format($montant, 0, ',', ' ') }} FCFA
            </div>

            <div class="receipt-box">
                <div class="receipt-row">
                    <span class="receipt-label">Référence Dépôt :</span>
                    <span class="receipt-value">{{ $depot->reference }}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Mode de paiement :</span>
                    <span class="receipt-value">{{ ucfirst(str_replace('_', ' ', $mode_paiement)) }}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Montant Total :</span>
                    <span class="receipt-value">{{ number_format($depot->prix_total, 0, ',', ' ') }} FCFA</span>
                </div>
                <!-- Recharge of relations might be needed in controller before passing the depot -->
                @php 
                  $totalPaye = $depot->paiements->sum('montant') ?? $montant; 
                  $resteAPayer = $depot->prix_total - $totalPaye;
                @endphp
                <div class="receipt-row">
                    <span class="receipt-label">Reste à payer :</span>
                    <span class="receipt-value" style="color: #ef4444;">{{ number_format($resteAPayer, 0, ',', ' ') }} FCFA</span>
                </div>
            </div>

            <div style="text-align: center; margin-top: 20px;">
                <p><strong>Statut du Dépôt :</strong> <br>
                    <span class="badge-status {{ $resteAPayer <= 0 ? 'status-paye' : 'status-partiel' }}" style="margin-top: 8px;">
                        {{ $resteAPayer <= 0 ? 'TOTALEMENT PAYÉ' : 'ACOMPTE VERSÉ' }}
                    </span>
                </p>
            </div>
        </div>
        <div class="footer">
            <p>Merci pour votre fidélité !</p>
            <p>&copy; {{ date('Y') }} WashPro. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
