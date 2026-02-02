@extends('layouts.main')
@section('content')
    <section class="flat-section pt-4 flat-property-detail">
        <div class="container border rounded shadow p-4">
            <div class="text-center mb-4">
                <div class="alert alert-success">
                    <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                    <h4>Paiement réussi!</h4>
                    <p class="mb-0">Le paiement a été effectué avec successe. Merci de votre confiance.</p>
                </div>
            </div>
            @php
                $start = \Carbon\Carbon::parse($reservation->start_time);
                $end = \Carbon\Carbon::parse($reservation->end_time);
                $totalMinutes = $start->diffInMinutes($end);
                $limit = $start->copy()->addMinutes($totalMinutes * 0.06);
                $date_limit = $limit->format('d/m/Y à H:i');
            @endphp
            <div class="card border-danger">
                <div class="card-header bg-danger-light text-white">
                    <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Reçu de Paiement</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <p class="text-muted mb-1">Numéro de réservation: <strong id="reservation-number"></strong></p>
                        <p class="text-muted">Date: <strong id="payment-date"></strong></p>
                    </div>
                    <div id="final-receipt">
                        <!-- Contenu généré dynamiquement -->
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <button class="btn btn-success btn-lg" onclick="downloadReceipt()">
                    <i class="fas fa-download me-2"></i>Télécharger le reçu
                </button>
            </div>

            <div class="single-property-element single-property-map">
                <div class="h7 title fw-7">Itinéraire</div>
                <div id="map-location-property-intinerary" class="map-single"
                    style="height:450px; border-radius:15px; overflow:hidden;"></div>

                <!-- Infos itinéraire -->
                <ul class="info-map mt-3 list-unstyled">
                    <li>
                        <div class="fw-7">📍 Adresse</div>
                        <span class="badge bg-light text-dark mt-2">{{ $reservation->property->address }}</span>
                    </li>
                    <li>
                        <div class="fw-7">📏 Distance</div>
                        <span class="badge bg-primary mt-2">Calcul en cours...</span>
                    </li>
                    <li>
                        <div class="fw-7">🚗 En Véhicule</div>
                        <span class="badge bg-success mt-2">Calcul en cours...</span>
                    </li>
                    <li>
                        <div class="fw-7">🚶 À pied</div>
                        <span class="badge bg-warning mt-2">Calcul en cours...</span>
                    </li>
                </ul>

                <!-- Boutons de mode de transport -->
                <div class="mt-3 d-flex gap-2 flex-wrap">
                    <a id="googleMapsBtn" target="_blank" class="btn btn-sm btn-outline-danger">📍 Google Maps</a>
                </div>
            </div>
        </div>

    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const reservationData = @json($reservation) || null;
            const dateLimit = @json($date_limit) || null;

            const reservationUuid = reservationData.uuid;
            let receiptDownloaded = false; // flag de validation

            function generateReceipt() {
                if (!reservationData) return;

                const r = reservationData;
                document.getElementById('reservation-number').textContent = r.code;
                document.getElementById('payment-date').textContent = new Date().toLocaleDateString('fr-FR');

                const start = new Date(r.start_time.replace(" ", "T"));
                const end = new Date(r.end_time.replace(" ", "T"));

                const receiptHTML = `
            <div class="d-flex justify-content-between mb-2"><span>Référence:</span><span>${r.code}</span></div>
            <div class="d-flex justify-content-between mb-2"><span>Date paiement:</span><span>${new Date().toLocaleString('fr-FR')}</span></div>
            
            <div class="d-flex justify-content-between mb-2"><span>Montant payé:</span><span>${Number(r.payment_amount).toLocaleString('fr-FR')} XOF</span></div>
            <div class="d-flex justify-content-between border-top pt-2 mb-2"><span>Statut:</span><span class="badge bg-success">Payé</span></div>
            <div class="mt-3 p-3 bg-light rounded">
                <h6>Détails</h6>
                <p class="mb-1"><strong>${r.prenoms} ${r.nom}</strong></p>
                <p class="mb-1">${r.email}</p>
                <p class="mb-1">${r.phone}</p>
                <p class="mb-0 mt-2">
                    ${r.sejour === 'Heure' ? `
                                                                Type: Réservation horaire<br>
                                                                Date: ${start.toLocaleDateString('fr-FR')}<br>
                                                                Heure de début: ${start.toLocaleTimeString('fr-FR', {hour: '2-digit', minute:'2-digit'})}<br>
                                                                Heure de fin: ${end.toLocaleTimeString('fr-FR', {hour: '2-digit', minute:'2-digit'})}<br>
                                                                Durée: ${r.nbr_of_sejour} heure(s)
                                                            ` : `
                                                                Type: Réservation journalière<br>
                                                                Arrivée: ${start.toLocaleString('fr-FR', {day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute:'2-digit'})}<br>
                                                                Départ: ${end.toLocaleString('fr-FR', {day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute:'2-digit'})}<br>
                                                                Nuits: ${r.nbr_of_sejour}
                                                            `}
                </p>
                <p class="mb-1 text-danger">NB: Afin de garantir votre réservation, merci de vous présenter au plus tard le <strong>${dateLimit}</strong>. En cas de retard, votre reservation sera automatiquement annulée.</p>
            </div>
        `;
                document.getElementById('final-receipt').innerHTML = receiptHTML;
            }

            // Exécuter à l'ouverture
            generateReceipt();

            // Fonction de téléchargement
            window.downloadReceipt = function() {
                if (reservationUuid) {
                    receiptDownloaded = true; // ✅ Marquer comme téléchargé
                    window.location.href = '/api/reservation/download-receipt/' + reservationUuid;
                } else {
                    alert("Réservation introuvable !");
                }
            }

            // Bloquer la fermeture si le reçu n’est pas téléchargé
            window.addEventListener('beforeunload', function(e) {
                if (!receiptDownloaded) {
                    e.preventDefault();
                    e.returnValue = "Veuillez télécharger votre reçu avant de quitter la page.";
                    return "Veuillez télécharger votre reçu avant de quitter la page.";
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const latitude = @json($reservation->property->latitude ?? 0);
            const longitude = @json($reservation->property->longitude ?? 0);

            // Fonds de carte
            const baseMaps = {
                "Clair": L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/">OSM</a> &copy; <a href="https://carto.com/">CARTO</a>'
                }),
                "Sombre": L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                    attribution: '&copy; OSM &copy; CARTO'
                }),
                "Classique": L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap'
                })
            };

            // Initialisation de la carte
            const map = L.map('map-location-property-intinerary', {
                center: [latitude, longitude],
                zoom: 15,
                layers: [baseMaps["Classique"]]
            });

            // Contrôle pour changer de fond
            L.control.layers(baseMaps).addTo(map);

            // Icônes personnalisées
            const propertyIcon = L.icon({
                iconUrl: "{{ asset('assets/images/location/map-icon.png') }}",
                iconSize: [45, 45],
                iconAnchor: [20, 38],
                popupAnchor: [0, -32]
            });

            // Marqueur de la propriété
            const propertyMarker = L.marker([latitude, longitude], {
                    icon: propertyIcon
                })
                .addTo(map)
                .bindPopup("🏠 Emplacement de la propriété")
                .openPopup();

            let userMarker, control;
            let currentMode = "driving";

            // Fonction mise à jour itinéraire
            function updateRoute(userLat, userLng) {
                if (control) map.removeControl(control);
                control = L.Routing.control({
                    waypoints: [L.latLng(userLat, userLng), L.latLng(latitude, longitude)],
                    router: L.Routing.osrmv1({
                        serviceUrl: 'https://router.project-osrm.org/route/v1'
                    }),
                    lineOptions: {
                        styles: [{
                            color: 'red',
                            weight: 5,
                            opacity: 0.8
                        }]
                    },
                    show: false,
                    addWaypoints: false
                }).addTo(map);

                control.on('routesfound', function(e) {
                    const route = e.routes[0];
                    const distanceKm = (route.summary.totalDistance / 1000).toFixed(2);
                    const durationMin = Math.round(route.summary.totalTime / 60);

                    document.querySelector('.info-map li:nth-child(2) span').innerText = distanceKm + " km";
                    document.querySelector('.info-map li:nth-child(3) span').innerText = durationMin +
                        " min en véhicule";
                    document.querySelector('.info-map li:nth-child(4) span').innerText = Math.round(
                        distanceKm * 12) + " min à pied";

                    // Lien Google Maps
                    document.getElementById('googleMapsBtn').href =
                        `https://www.google.com/maps/dir/?api=1&origin=${userLat},${userLng}&destination=${latitude},${longitude}&travelmode=${currentMode}`;
                });
            }

            // Bouton recentrer
            L.control.locate({
                position: 'topleft',
                strings: {
                    title: "Recentrer sur ma position"
                },
                flyTo: true,
                keepCurrentZoomLevel: false
            }).addTo(map);

            // Suivi position utilisateur
            if (navigator.geolocation) {
                navigator.geolocation.watchPosition(position => {
                    const userLat = position.coords.latitude;
                    const userLng = position.coords.longitude;

                    const userIcon = L.divIcon({
                        className: 'user-location-marker',
                        html: `<div class="user-marker" style="
                            width: 25px;
                            height: 25px;
                            background: radial-gradient(circle, #007bff 30%, #0056b3 70%);
                            border-radius: 50%;
                            border: 2px solid white;
                            box-shadow: 0 0 8px rgba(0, 123, 255, 0.8);
                        "></div>`,
                        iconSize: [25, 25],
                        iconAnchor: [12, 12]
                    });

                    if (!userMarker) {
                        userMarker = L.marker([userLat, userLng], {
                                icon: userIcon
                            }) // ici ton icône perso
                            .addTo(map)
                            .bindPopup("📍 Votre position")
                            .openPopup();
                    } else {
                        userMarker.setLatLng([userLat, userLng]); // mise à jour fluide
                    }

                    updateRoute(userLat, userLng);
                }, () => {
                    alert("Impossible de récupérer votre position GPS.");
                }, {
                    enableHighAccuracy: true
                });
            }
        });
    </script>
@endsection
