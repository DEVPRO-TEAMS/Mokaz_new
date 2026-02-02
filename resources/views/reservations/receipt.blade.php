<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Reçu de Réservation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 10px;
            /* réduit la marge pour économiser de l’espace */
            color: #333;
            font-size: 12px;
            /* taille globale réduite */
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #dc3545;
            padding-bottom: 5px;
            page-break-inside: avoid;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            color: #dc3545;
            margin: 5px 0;
        }

        .section {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
            margin-bottom: 5px;
            color: #dc3545;
        }

        .info-row {
            display: flex;
            margin-bottom: 5px;
            font-size: 12px;
        }

        .info-label {
            width: 120px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 12px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 10px;
            color: #666;
            page-break-inside: avoid;
        }

        .total-row {
            background-color: #f8f9fa;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header" style="width: 25%; margin: 0 auto">
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/images/logo/logo-main.png'))) }}"
            style="width: 100%" alt="MOKAZ Logo" class="logo">
    </div>
    <div class="header">
        <div class="title">Reçu de Réservation</div>
        <div>Date: {{ $date }}</div>
        <div style="font-weight: bold; margin-top: 5px">Référence: {{ $reservation->code }}</div>

    </div>

    <div class="section" style="height: 100px">
        <div class="section-title">Informations Client</div>
        <div style="width: 100%;">
            <div style="width: 50%; float: left">
                <div class="info-row">
                    <div class="info-label">Nom:</div>
                    <div>{{ $reservation->nom }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Prenoms:</div>
                    <div>{{ $reservation->prenoms }}</div>
                </div>
            </div>
            <div style="width: 50%; float: right">
                <div class="info-row">
                    <div class="info-label">Email:</div>
                    <div>{{ $reservation->email }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Téléphone:</div>
                    <div>{{ $reservation->phone }}</div>
                </div>
            </div>

        </div>

    </div>

    <div class="section" style="height: 155px">
        <div class="section-title">Détails de la Réservation</div>
        <div style="width: 100%;">
            @if ($reservation->sejour == 'Heure')
                <div style="width: 50%; float: left">
                    <div class="info-row">
                        <div class="info-label">Type:</div>
                        <div>Réservation horaire</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Durée:</div>
                        <div>{{ $reservation->nbr_of_sejour }} heure(s)</div>
                    </div>
                </div>
                <div style="width: 50%; float: right">
                    <div class="info-row">
                        <div class="info-label">Arrivée:</div>
                        <div>{{ \Carbon\Carbon::parse($reservation->start_time)->format('d/m/Y à H:i') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Départ:</div>
                        <div>{{ \Carbon\Carbon::parse($reservation->end_time)->format('d/m/Y à H:i') }}</div>
                    </div>
                </div>
            @else
                <div style="width: 50%; float: left">
                    <div class="info-row">
                        <div class="info-label">Type:</div>
                        <div>Réservation journalière</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Nuits:</div>
                        <div>{{ $reservation->nbr_of_sejour }}</div>
                    </div>
                </div>
                <div style="width: 50%; float: right">
                    <div class="info-row">
                        <div class="info-label">Arrivée:</div>
                        <div>{{ \Carbon\Carbon::parse($reservation->start_time)->format('d/m/Y à H:i') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Départ:</div>
                        <div>{{ \Carbon\Carbon::parse($reservation->end_time)->format('d/m/Y à H:i') }}</div>
                    </div>
                </div>
            @endif
        </div>

        <div class="info-row" style="margin-top: 5px; width: 100%">
            <div class="info-label">NB:</div>
            <div>
                Afin de garantir votre réservation, merci de vous présenter au plus tard le
                @php
                    $start = \Carbon\Carbon::parse($reservation->start_time);
                    $end = \Carbon\Carbon::parse($reservation->end_time);
                    $totalMinutes = $start->diffInMinutes($end);
                    $limit = $start->copy()->addMinutes($totalMinutes * 0.06);
                @endphp
                <strong>{{ $limit->format('d/m/Y à H:i') }}</strong> sinon votre réservation sera automatiquement
                annulée.
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Détails de Paiement</div>
        <table>
            <tr>
                <th>Description</th>
                <th>Montant (XOF)</th>
            </tr>
            <tr>
                <td>Prix {{ $reservation->sejour == 'Heure' ? 'horaire' : 'journalier' }}</td>
                <td>{{ number_format($reservation->unit_price, 0, ',', ' ') }}</td>
            </tr>
            <tr>
                <td>Quantité</td>
                <td>{{ $reservation->nbr_of_sejour }}</td>
            </tr>
            <tr class="total-row">
                <td><strong>Total à payer</strong></td>
                <td><strong>{{ number_format($reservation->total_price, 0, ',', ' ') }}</strong></td>
            </tr>
            <tr>
                <td>Montant payé</td>
                <td>{{ number_format($reservation->payment_amount, 0, ',', ' ') }}</td>
            </tr>
            @if ($reservation->paiement)
                <tr>
                    <td>Mode de paiement</td>
                    <td>
                        @switch($reservation->paiement->payment_mode)
                            @case('PAIEMENTMARCHANDOMPAYCIDIRECT')
                                <span style="color: #FFA500; font-weight: bold; padding: 5px; border-radius: 5px">Orange
                                    Money</span>
                            @break

                            @case('PAIEMENTMARCHAND_MTN_CI')
                                <span
                                    style="color: #ffee00; font-weight: bold; padding: 5px; border-radius: 5px">MTN
                                    Money</span>
                            @break

                            @case('PAIEMENTMARCHAND_MOOV_CI')
                                <span
                                    style="color: #005eff; font-weight: bold; padding: 5px; border-radius: 5px">Moov
                                    Money</span>
                            @break

                            @case('CI_PAIEMENTWAVE_TP')
                                <span
                                    style="color: #00b3ff; font-weight: bold; padding: 5px; border-radius: 5px">Wave</span>
                            @break

                            @default
                                <span
                                    style="color: #444; font-weight: bold; padding: 5px; border-radius: 5px">{{ $reservation->paiement->payment_mode }}</span>
                        @endswitch


                    </td>
                </tr>
            @endif
            <tr>
                <td>Reste à payer</td>
                <td>{{ number_format((float) $reservation->total_price - (float) $reservation->payment_amount, 0, ',', ' ') }}
                </td>
            </tr>
            @if ($reservation->paiement)
                <tr>
                    <td>Statut</td>
                    <td>
                        @switch($reservation->paiement->payment_status)
                            @case('paid')
                                <span style="color: green;">Payé</span>
                            @break

                            @case('pending' || 'unpaid')
                                <span style="color: red;">Non payé</span>
                            @break

                            @default
                                <span
                                    style="background-color: #444a4da1 !important; color: #444; font-weight: bold; padding: 5px; border-radius: 5px">{{ $reservation->paiement->payment_status }}</span>
                        @endswitch
                    </td>
                </tr>
            @endif
        </table>
    </div>

    <div class="footer">
        <p>Merci pour votre réservation !</p>
        <p>Pour toute question, contactez-nous à info@jsbeyci.com</p>
    </div>
</body>

</html>
