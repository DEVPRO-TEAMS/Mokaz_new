<div class="content">
    <!-- Code de réservation -->
    <div class="reservation-code">
        <h2>{{ $reservation->code }}</h2>
        <p style="margin: 5px 0 0 0; color: #6c757d;">Code de la réservation annulée</p>
    </div>

    <!-- Salutation -->
    <p style="font-size: 16px; margin-bottom: 20px;">
        Bonjour <strong>{{ $reservation->nom }} {{ $reservation->prenoms }}</strong>,
    </p>

    <p style="font-size: 16px; line-height: 1.6; color: #495057;">
        Votre réservation a été <strong>annulée</strong>.
        Nous sommes désolés que vous ne puissiez plus profiter de votre séjour prévu.
    </p>

    <!-- Informations d'annulation -->
    <div class="cancellation-info">
        <h3><span class="icon">❌</span>Détails de l'annulation</h3>
        <div class="info-row">
            <span class="info-label">Date d'annulation :</span>
            <span class="info-value">{{ \Carbon\Carbon::now()->translatedFormat('l d F Y à H:i') }}</span>
        </div>
        @php
            $start = \Carbon\Carbon::parse($reservation->start_time);
            $end = \Carbon\Carbon::parse($reservation->end_time);
            $totalMinutes = $start->diffInMinutes($end);
            $limit = $start->copy()->addMinutes($totalMinutes * 0.1);
        @endphp
        <div class="info-row">
            <span class="info-label">Motif :</span>
            <span class="info-value">
                @if(!$reservation->is_present)
                    Client absent après le {{ $limit->translatedFormat('l d F Y à H:i') }}
                @else
                    Annulation à la demande du client
                @endif
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Annulé par :</span>
            <span class="info-value">
                {{ !$reservation->is_present ? 'Annulation automatique du système' : 'Client' }}
            </span>
        </div>
    </div>

    <!-- Dates annulées -->
    <div class="cancelled-dates">
        <h3 style="margin-top: 0;">📅 Séjour annulé</h3>
        <div class="date-item">
            <span><strong>Arrivée prévue :</strong></span>
            <span>{{ \Carbon\Carbon::parse($reservation->start_time)->translatedFormat('l d F Y à H:i') }}</span>
        </div>
        <div class="date-item">
            <span><strong>Départ prévu :</strong></span>
            <span>{{ \Carbon\Carbon::parse($reservation->end_time)->translatedFormat('l d F Y à H:i') }}</span>
        </div>
        <div class="date-item">
            <span><strong>Durée :</strong></span>
            <span>
                @php
                    $start_time = \Carbon\Carbon::parse($reservation->start_time->format('Y-m-d H:i'));
                    $end_time = \Carbon\Carbon::parse($reservation->end_time->format('Y-m-d H:i'));
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

    <!-- Informations logement annulé -->
    <div class="info-section">
        <h3><span class="icon">🏠</span>Logement concerné</h3>
        <div class="info-row">
            <span class="info-label">Propriété :</span>
            <span class="info-value">{{ $reservation->property->title ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Hébergement/Chambre :</span>
            <span class="info-value">{{ $reservation->appartement->title ?? 'N/A' }}</span>
        </div>
        @if ($reservation->partner_uuid)
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

    <!-- Informations de remboursement -->
    {{-- <div class="refund-summary">
        <h3>💰 Informations de remboursement</h3>
        <div class="info-row">
            <span class="info-label">Montant payé :</span>
            <span class="info-value">{{ number_format($reservation->payment_amount, 0, ',', ' ') }} FCFA</span>
        </div>
        <div class="info-row">
            <span class="info-label">Frais d'annulation :</span>
            <span class="info-value">{{ number_format($cancellation_fees ?? 0, 0, ',', ' ') }} FCFA</span>
        </div>
        <div class="info-row">
            <span class="info-label">Mode de paiement original :</span>
            <span class="info-value">{{ ucfirst($reservation->payment_method) }}</span>
        </div>

        <div class="refund-amount">
            Montant à rembourser :
            {{ number_format($reservation->payment_amount - ($cancellation_fees ?? 0), 0, ',', ' ') }} FCFA
        </div>

        <div class="status-cancelled">
            💳 Le remboursement sera effectué sous 3-5 jours ouvrés sur votre mode de paiement original
        </div>
    </div> --}}

    <!-- Notes client -->
    @if ($reservation->notes)
        <div class="info-section">
            <h3><span class="icon">📝</span>Notes de la réservation</h3>
            <div style="font-style: italic; color: #6c757d;">
                "{{ $reservation->notes }}"
            </div>
        </div>
    @endif

    <!-- Informations importantes -->
    <div class="important-note">
        <h4>ℹ️ Informations importantes</h4>
        <ul style="margin: 0; padding-left: 20px;">
            <li>Votre réservation est définitivement annulée</li>
            <li>Le logement est maintenant disponible pour d'autres clients</li>
            {{-- <li>Le remboursement sera traité automatiquement</li> --}}
            {{-- <li>Vous recevrez une confirmation du remboursement par email</li> --}}
            <li>En cas de question, notre service client reste à votre disposition</li>
        </ul>
    </div>

    <!-- Bouton contact support -->
    <div style="text-align: center; margin: 30px 0;">
        <a href="mailto:info@jsbeyci.com" class="btn-support">💬 Contacter le support</a>
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
        Nous espérons avoir l'occasion de vous accueillir prochainement !<br>
        <strong>L'équipe MOKAZ</strong>
    </p>
</div>
