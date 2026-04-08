<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu - {{ $depot->reference }}</title>
    <style>
        @page { margin: 0; size: 80mm auto; orientation: portrait; }
        body { 
            font-family: 'Courier New', Courier, monospace; 
            background-color: #f8fafc; 
            margin: 0; 
            padding: 20px;
            color: #000;
        }
        .receipt-container {
            width: 80mm;
            max-width: 80mm;
            margin: 0 auto;
            background: white;
            padding: 10px;
            box-sizing: border-box;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .mb-1 { margin-bottom: 5px; }
        .mb-2 { margin-bottom: 10px; }
        .mb-3 { margin-bottom: 15px; }
        .mt-2 { margin-top: 10px; }
        .mt-3 { margin-top: 15px; }
        .pb-2 { padding-bottom: 10px; }
        .border-bottom { border-bottom: 1px dashed #000; }
        .border-top { border-top: 1px dashed #000; }
        
        h1, h2, h3, p { margin: 0; }
        h1 { font-size: 20px; font-weight: 900; letter-spacing: -1px; }
        h2 { font-size: 14px; margin-top: 5px; font-weight: bold; }
        .small { font-size: 11px; }
        .normal { font-size: 12px; line-height: 1.3; }
        
        .totals-grid {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 5px;
            padding-top: 10px;
            font-size: 12px;
        }
        
        table { width: 100%; border-collapse: collapse; margin-top: 5px; font-size: 11px; }
        td { padding: 4px 0; vertical-align: top; }
        td:last-child { text-align: right; font-weight: bold; }

        .btn-print {
            display: block;
            width: 80mm;
            margin: 20px auto;
            padding: 10px;
            background: #198754;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            font-family: sans-serif;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }

        @media print {
            body { background-color: white; padding: 0; font-family: 'Courier New', Courier, monospace; width: 80mm; }
            .receipt-container { box-shadow: none; margin: 0; padding: 5px; width: 80mm; border: none; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    @php
        $backUrl = '#';
        if (Auth::check()) {
            $backUrl = Auth::user()->role === 'client' ? route('client.dashboard') : route('paiements.index');
        }
    @endphp
    <div class="no-print" style="display: flex; gap: 10px; width: 80mm; margin: 20px auto; justify-content: space-between;">
        <a href="{{ $backUrl }}" class="btn-print" style="margin: 0; background-color: #6c757d; flex: 1; text-decoration: none;">⬅️ RETOUR</a>
        <button onclick="window.print()" class="btn-print" style="margin: 0; flex: 1;">🖨️ IMPRIMER</button>
    </div>

    <div class="receipt-container">
        <!-- HEADER -->
        <div class="text-center mb-3 pb-2 border-bottom">
            <h1>WASHPRO</h1>
            <p class="normal">Douala, Village Ndogpassi, Cameroun</p>
            <p class="small mt-2">Tel: +237 698 25 57 25</p>
        </div>

        <!-- INFO -->
        <div class="text-center mb-2">
            <h2>REÇU DE PAIEMENT</h2>
        </div>
        <div class="normal mb-3 border-bottom pb-2">
            <div class="mb-1"><span class="font-bold">Dépôt N°:</span> {{ $depot->reference }}</div>
            <div class="mb-1"><span class="font-bold">Date:</span> {{ now()->format('d/m/Y H:i') }}</div>
            <div class="mb-1"><span class="font-bold">Client:</span> {{ $depot->client->prenom ?? '' }} {{ $depot->client->nom ?? '' }}</div>
        </div>

        <!-- TOTALS & PAYMENTS SUMMARY -->
        <div class="totals-grid mb-2">
            <div>Montant Total Facture:</div>
            <div>{{ number_format($depot->prix_total, 0, ',', ' ') }} F</div>
            
            @php $totalPaye = $depot->paiements->sum('montant'); @endphp
            <div>Total Payé à ce jour:</div>
            <div>-{{ number_format($totalPaye, 0, ',', ' ') }} F</div>
            
            <div class="font-bold mt-2">RESTE À PAYER:</div>
            <div class="font-bold mt-2">{{ number_format($depot->reste_a_payer, 0, ',', ' ') }} F</div>
        </div>

        <div class="border-top mt-3 pt-2">
            <p class="font-bold normal mb-1 text-center">VERSEMENTS RECENTS</p>
            <table>
                <tbody>
                    @foreach($depot->paiements as $p)
                    <tr class="border-bottom">
                        <td>{{ \Carbon\Carbon::parse($p->date_paiement)->format('d/m/y') }} <br><span class="small" style="color: #555;">({{ str_replace('_', ' ', $p->mode_paiement) }})</span></td>
                        <td>{{ number_format($p->montant, 0, ',', ' ') }} F</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- STATUS BAR -->
        <div class="text-center py-2 mt-3 font-bold" style="font-size: 13px; border-top: 2px solid #000; border-bottom: 2px solid #000;">
            @if($depot->etat_paiement == 'payé')
                [ SOLDÉ ]
            @elseif($depot->etat_paiement == 'partiel')
                [ PARTIEL ]
            @else
                [ NON PAYÉ ]
            @endif
        </div>

        <!-- FOOTER -->
        <div class="text-center small mt-3 mb-2">
            <p class="font-bold">Merci de votre confiance !</p>
        </div>
        
    </div>
</body>
</html>
