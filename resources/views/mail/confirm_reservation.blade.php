<div class="content">
    <!-- Code de réservation -->
    <div class="reservation-code">
        <h2>{{ $reservation->code }}</h2>
        <p style="margin: 5px 0 0 0; color: #6c757d;">Votre code de réservation</p>
    </div>

    <!-- Salutation -->
    <p style="font-size: 16px; margin-bottom: 20px;">
        Hello <strong>{{ $reservation->nom }} {{ $reservation->prenoms }}</strong>,
    </p>

    <p style="font-size: 16px; line-height: 1.6; color: #495057;">
        Nous avons le plaisir de vous confirmer votre réservation. Voici tous les détails de votre séjour :
    </p>

    <!-- Dates et horaires -->
    <div class="highlight-dates">
        <h3 style="margin-top: 0;">📅 Dates de votre séjour</h3>
        <div class="date-item">
            <span><strong>Arrivée :</strong></span>
            <span>{{ \Carbon\Carbon::parse($reservation->start_time)->translatedFormat('l d F Y à H:i') }}</span>
        </div>
        <div class="date-item">
            <span><strong>Départ :</strong></span>
            <span>{{ \Carbon\Carbon::parse($reservation->end_time)->translatedFormat('l d F Y à H:i') }}</span>
        </div>
        <div class="date-item">
            <span><strong>Durée :</strong></span>
            <span>
                @php
                    $start_time = \Carbon\Carbon::parse($reservation->start_time->format('Y-m-d H:i'));
                    $end_time =  \Carbon\Carbon::parse($reservation->end_time->format('Y-m-d H:i'));
                    $duration = $start_time->diff($end_time);
                    if ($reservation->sejour === 'Heure') {
                        echo $duration->h . 'h ' . $duration->i . 'min';
                    } else {
                        echo $duration->days . ' jour(s)';
                    }
                @endphp
            </span>
            
        </div>
    </div>

    <!-- Informations logement -->
    <div class="info-section">
        <h3><span class="icon">🏠</span>Votre logement</h3>
        <div class="info-row">
            <span class="info-label">Propriété :</span>
            <span class="info-value">{{ $reservation->property->title ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Hébergement/Chambre :</span>
            <span class="info-value">{{ $reservation->appartement->title ?? 'N/A' }}</span>
        </div>
        @if($reservation->partner_uuid)
        <div class="info-row">
            <span class="info-label">Chez :</span>
            <span class="info-value">{{ $reservation->partner->raison_social ?? 'N/A' }}</span>
        </div>
        @endif
        <div class="info-row">
            <span class="info-label">Type de séjour :</span>
            <span class="info-value">{{ $reservation->sejour }}</span>
        </div>
    </div>

    <!-- Informations financières -->
    <div class="price-summary">
        <h3>💰 Résumé financier</h3>
        <div class="info-row">
            <span class="info-label">Prix unitaire :</span>
            <span class="info-value">{{ number_format($reservation->unit_price, 0, ',', ' ') }} FCFA</span>
        </div>
        <div class="info-row">
            <span class="info-label">Nombre de {{ strtolower($reservation->sejour) }}(s) :</span>
            <span class="info-value">{{ $reservation->nbr_of_sejour }}</span>
        </div>
        {{-- <div class="info-row">
            <span class="info-label">Mode de paiement :</span>
            <span class="info-value">{{ ucfirst($reservation->payment_method) }}</span>
        </div> --}}

        <div class="total-price">
            Total : {{ number_format($reservation->total_price, 0, ',', ' ') }} FCFA
        </div>

        @if($reservation->paiement->payment_status === 'paid')
        <div class="status-partial">
            ⚠️ Accompte : {{ number_format($reservation->payment_amount, 0, ',', ' ') }} FCFA payés<br>
            <strong>Reste à payer : {{ number_format($reservation->still_to_pay, 0, ',', ' ') }} FCFA</strong>
        </div>
        @elseif($reservation->paiement->payment_status === 'pending' || $reservation->paiement->payment_status === 'unpaid')
        <div class="status-partial">
            ⚠️ Paiement en attente de confirmation
        </div>
        @endif
    </div>

    <!-- Notes client -->
    @if($reservation->notes)
    <div class="info-section">
        <h3><span class="icon">📝</span>Vos notes</h3>
        <div style="font-style: italic; color: #6c757d;">
            "{{ $reservation->notes }}"
        </div>
    </div>
    @endif

    <!-- Informations importantes -->
    <div class="important-note">
        <h4>ℹ️ Informations importantes</h4>
        <ul style="margin: 0; padding-left: 20px;">
            <li>Veuillez vous présenter avec une pièce d'identité valide</li>
            <li>L'heure d'arrivée standard est à partir de {{
                \Carbon\Carbon::parse($reservation->start_time)->format('H:i') }}</li>
            <li>L'heure de départ est fixée à {{ \Carbon\Carbon::parse($reservation->end_time)->format('H:i') }}
            </li>
            <li>En cas de retard ou de problème, contactez-nous immédiatement</li>
            @if($reservation->still_to_pay > 0)
            <li><strong>N'oubliez pas de régler le solde de {{ number_format($reservation->still_to_pay, 0, ',', '
                    ') }} FCFA lors de votre arrivée</strong></li>
            @endif
        </ul>
    </div>

    <!-- Bouton de téléchargement -->
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ url($reservation->receipt->filepath) }}" download class="btn-download">📄
            Télécharger le reçu</a>
    </div>
    <!-- Informations de contact -->
    <div class="contact-info">
        <h4 style="margin-top: 0; color: #495057;">📞 Besoin d'aide ?</h4>
        <p style="margin: 5px 0;">
            <strong>Service client :</strong><br>
            Téléphone : <a href="tel:+2250787245197" style="color: #dc3545;">+225 07 87 24 51 97</a><br>
            Email : <a href="mailto:info@jsbeyci.com" style="color: #dc3545;">info@jsbeyci.com</a>
        </p>
        <p style="margin: 15px 0 0 0; font-size: 14px; color: #6c757d;">
            Nous sommes disponibles 24h/24 et 7j/7 pour vous accompagner
        </p>
    </div>

    <p style="font-size: 16px; text-align: center; margin-top: 30px;">
        Nous vous souhaitons un excellent séjour !<br>
        <strong>L'équipe MOKAZ</strong>

    </p>
</div>