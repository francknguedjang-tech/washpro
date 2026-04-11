{{-- Nom : emails/depot-confirmation.blade.php | Rôle : Notification envoyée au client après avoir déposé son linge --}}
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Inter', sans-serif; line-height: 1.6; color: #1e293b; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4361ee; color: white; padding: 30px; text-align: center; border-radius: 12px 12px 0 0; }
        .content { background: #f8fafc; padding: 30px; border-radius: 0 0 12px 12px; border: 1px solid #e2e8f0; }
        .footer { text-align: center; margin-top: 20px; color: #64748b; font-size: 0.8rem; }
        .btn { display: inline-block; padding: 12px 24px; background: #4361ee; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; margin-top: 20px; }
        .code-box { background: #fff; border: 2px dashed #4361ee; padding: 15px; text-align: center; font-size: 1.5rem; letter-spacing: 5px; font-weight: 800; color: #4361ee; margin: 20px 0; border-radius: 8px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 20px; }
        .info-item { background: white; padding: 10px; border-radius: 8px; border: 1px solid #e2e8f0; }
        .info-label { font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: 700; }
        .info-value { font-weight: 600; color: #1e293b; }
    </style>
</head>
<body>
    <div class="header">
        <h1>WashPro</h1>
        <p>Confirmation de Dépôt</p>
    </div>
    <div class="content">
        <p>Bonjour <strong>{{ $depot->client->prenom }}</strong>,</p>
        <p>Votre dépôt a été enregistré avec succès chez WashPro.</p>
        
        <div class="code-box">
            <small style="display:block; font-size: 0.8rem; letter-spacing: normal; margin-bottom: 5px;">CODE DE RETRAIT</small>
            {{ $depot->code_retrait }}
        </div>

        <p style="text-align: center; font-style: italic; font-size: 0.9rem;">
            ⚠️ <strong>Important :</strong> Présentez ce code (ou confiez-le à la personne de votre choix) pour retirer votre linge en toute sécurité.
        </p>

        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Référence Dépôt</div>
                <div class="info-value">#{{ str_pad($depot->id, 5, '0', STR_PAD_LEFT) }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Retrait Prévu</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($depot->date_retrait_prevue)->format('d/m/Y H:i') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Articles</div>
                <div class="info-value">{{ $depot->linges->count() }} vêtement(s)</div>
            </div>
            <div class="info-item">
                <div class="info-label">Total à payer</div>
                <div class="info-value">{{ number_format($depot->prix_total, 0, ',', ' ') }} FCFA</div>
            </div>
        </div>
    </div>
    <div class="footer">
        <p>&copy; {{ date('Y') }} WashPro. Tous droits réservés.</p>
    </div>
</body>
</html>
