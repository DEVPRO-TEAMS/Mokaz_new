<div class="content">
    <!-- Code de réservation -->
    <div class="reservation-code">
        <h2>{{ $reservation->code }}</h2>
        <p style="margin: 5px 0 0 0; color: #6c757d;">Code de votre séjour terminé</p>
    </div>

    <!-- Salutation -->
    <p style="font-size: 16px; margin-bottom: 20px;">
        Cher/Chère <strong>{{ $reservation->nom }} {{ $reservation->prenoms }}</strong>,
    </p>

    <p style="font-size: 16px; line-height: 1.6; color: #495057;">
        Nous espérons que vous avez passé un <strong>excellent séjour</strong> !
        Votre satisfaction est notre priorité et nous serions ravis d'avoir votre retour sur votre expérience.
    </p>

    <!-- Remerciements -->
    <div class="thank-you-summary">
        <h3><span class="icon">🙏</span>Merci pour votre confiance</h3>
        <p style="margin-bottom: 15px;">
            Nous tenons à vous remercier d'avoir choisi MOKAZ pour votre reservation chez {{ $reservation->partner->raison_social }}.
            Votre présence contribue au développement de notre plateforme et nous motive à offrir le meilleur service
            possible.
        </p>
        <div class="highlight-text">
            ⭐ Votre avis compte énormément pour nous ! ⭐
        </div>
    </div>

    <!-- Dates du séjour terminé -->
    <div class="completed-dates">
        <h3 style="margin-top: 0;">📅 Votre séjour terminé</h3>
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

    <!-- Section feedback -->
    <div class="feedback-section">
        <h3>💬 Partagez votre expérience</h3>
        <p style="margin-bottom: 15px;">
            Votre avis nous aide à améliorer nos services et guide les futurs voyageurs dans leur choix.
            Nous serions reconnaissants si vous pouviez prendre quelques minutes pour noter votre séjour.
        </p>
        <div style="background: white; padding: 15px; border-radius: 8px; margin: 15px 0;">
            <strong>Ce que vous pouvez évaluer :</strong>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>La qualité de l'hébergement</li>
                <li>La propreté des lieux</li>
                <li>L'accueil et le service</li>
                <li>Le rapport qualité-prix</li>
                <li>Votre expérience globale</li>
            </ul>
        </div>
    </div>

    <!-- Notes client -->
    @if ($reservation->notes)
        <div class="info-section">
            <h3><span class="icon">📝</span>Vos notes de réservation</h3>
            <div style="font-style: italic; color: #6c757d;">
                "{{ $reservation->notes }}"
            </div>
        </div>
    @endif

    <!-- Informations importantes -->
    <div class="important-note">
        <h4>🎁 Offres spéciales pour votre prochain séjour</h4>
        <ul style="margin: 0; padding-left: 20px;">
            {{-- <li>Réduction de 10% sur votre prochaine réservation (code : RETOUR10)</li> --}}
            <li>Programme de fidélité : cumulez des points à chaque séjour</li>
            <li>Notifications des nouvelles propriétés dans vos zones préférées</li>
            {{-- <li>Accès prioritaire aux offres et promotions exclusives</li> --}}
            <li>Support client dédié pour nos clients fidèles</li>
        </ul>
    </div>

    <!-- Bouton pour laisser un avis -->
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ url('/detail/appartement/'.$reservation->appartement->uuid) }}#comments" class="btn-review">⭐ Noter
            l'hébergement</a>
        <p style="margin-top: 10px; font-size: 14px; color: #6c757d;">
            Cela ne prend que 2 minutes et aide vraiment la communauté !
        </p>
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
        Merci encore pour votre confiance et à bientôt sur MOKAZ !<br>
        <strong>L'équipe MOKAZ</strong>
    </p>
</div>
