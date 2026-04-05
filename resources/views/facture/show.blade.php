<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture #{{ $depot->id }}</title>
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Courier New', Courier, monospace; }
        .invoice-container { 
            width: 80mm; 
            margin: 20px auto; 
            background: white; 
            padding: 5mm; 
            box-shadow: 0 0 10px rgba(0,0,0,0.1); 
        }
        .header { text-align: center; margin-bottom: 10px; }
        .header h1 { font-size: 18px; font-weight: 900; margin: 0; }
        .divider { border-top: 1px dashed #000; margin: 10px 0; }
        .detail-row { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 3px; }
        .detail-label { font-weight: bold; }
        
        .table-items { width: 100%; margin-top: 10px; font-size: 12px; border-collapse: collapse; }
        .table-items th { border-bottom: 1px solid #000; text-align: left; padding-bottom: 5px; }
        .table-items td { padding: 5px 0; }
        
        .total-box { margin-top: 15px; border-top: 1px solid #000; padding-top: 10px; }
        .total-row { display: flex; justify-content: space-between; font-size: 14px; font-weight: bold; }
        .footer { text-align: center; font-size: 10px; margin-top: 20px; }

        @media print {
            body { background: white; padding: 0; margin: 0; }
            .no-print { display: none !important; }
            .invoice-container { 
                width: 80mm; 
                margin: 0; 
                padding: 2mm; 
                box-shadow: none; 
            }
            @page { margin: 0; size: 80mm auto; }
        }
    </style>
</head>
<body>
    <div class="container text-center mt-3 no-print">
        <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 fw-bold">
            <i class="bi bi-printer me-2"></i> Imprimer Facture (80mm)
        </button>
        <a href="{{ route('depots.show', $depot->id) }}" class="btn btn-light rounded-pill px-4 fw-bold ms-2">
            Retour
        </a>
    </div>

    <div class="invoice-container">
        <div class="header">
            <h1>WASHPRO</h1>
            <p style="font-size: 12px;">Facture Client</p>
        </div>

        <div class="divider"></div>

        <div class="detail-row">
            <span class="detail-label">FACT N°:</span>
            <span>#F-{{ str_pad($depot->id, 5, '0', STR_PAD_LEFT) }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">DATE:</span>
            <span>{{ \Carbon\Carbon::parse($depot->date_depot)->format('d/m/Y') }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">CLIENT:</span>
            <span>{{ $depot->client?->nom }}</span>
        </div>

        <table class="table-items">
            <thead>
                <tr>
                    <th>Désign.</th>
                    <th style="text-align: center;">Qté</th>
                    <th style="text-align: right;">Prix</th>
                </tr>
            </thead>
            <tbody>
                @foreach($depot->linges as $linge)
                <tr>
                    <td>{{ $linge->designation }}</td>
                    <td style="text-align: center;">{{ $linge->quantite }}</td>
                    <td style="text-align: right;">{{ number_format($depot->prix_total / count($depot->linges), 0, ',', ' ') }}</td>
                </tr>
                @endforeach
                @if($depot->linges->count() == 0)
                <tr>
                    <td>{{ $depot->service?->type_service }}</td>
                    <td style="text-align: center;">1</td>
                    <td style="text-align: right;">{{ number_format($depot->prix_total, 0, ',', ' ') }}</td>
                </tr>
                @endif
            </tbody>
        </table>

        <div class="total-box">
            <div class="total-row">
                <span>TOTAL:</span>
                <span>{{ number_format($depot->prix_total, 0, ',', ' ') }} F</span>
            </div>
            <div class="total-row" style="font-size: 12px; font-weight: normal; margin-top: 5px;">
                <span>Payé:</span>
                <span>{{ number_format($depot->totalPaye(), 0, ',', ' ') }} F</span>
            </div>
            <div class="total-row" style="margin-top: 5px; color: #d00;">
                <span>RESTE:</span>
                <span>{{ number_format($depot->resteAPayer(), 0, ',', ' ') }} F</span>
            </div>
        </div>

        <div class="divider"></div>

        <div class="footer">
            Conservez cette facture pour le retrait.<br>
            Tout retrait exige ce ticket.<br>
            Merci de votre fidélité !
        </div>
    </div>
    
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}">
</body>
</html>
