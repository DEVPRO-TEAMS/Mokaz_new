@extends('layouts.main')
@section('content')
    <section class="flat-section pt-4 flat-property-detail">
        <div class="container border rounded shadow p-4">
            <div class="text-center mb-4">
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle fa-3x mb-3 text-danger"></i>
                    <h4>Paiement échoué!</h4>
                    <p class="mb-0">Le paiement de cette reservation a echoué. Veuillez essayer de nouveau.</p>
                </div>
            </div>

            @php
                $start = \Carbon\Carbon::parse($reservation->start_time);
                $end = \Carbon\Carbon::parse($reservation->end_time);
                $totalMinutes = $start->diffInMinutes($end);
                $limitTotal = $start->copy()->addMinutes($totalMinutes * 0.05);
                $dateLimitTotal = $limitTotal->format('d/m/Y à H:i');
            @endphp
            <div class="card border-danger">
                <div class="card-header bg-danger-light text-white">
                    <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>details de la reservation</h5>
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
                <button onclick="processPayment()" class="btn btn-success btn-lg">
                    <i class="fas fa-credit-card me-2"></i>Payer maintenant
                </button>
            </div>
        </div>
    </section>

    <script type="text/javascript">
        const reservationData = @json($reservation) || null;
        const dateLimitTotalStr = @json($dateLimitTotal) || null;
        
        
        let urlWaiting = "{{ route('reservation.paiement.waiting', ['reservation_uuid' => ':reservation_uuid']) }}";
        let urlFailed = "{{ route('reservation.paiement.failed', ['reservation_uuid' => ':reservation_uuid']) }}";
        const reservationUuid = reservationData.uuid;
        document.addEventListener('DOMContentLoaded', function() {

            function generateReceipt() {
                if (!reservationData) return;

                const r = reservationData;
                document.getElementById('reservation-number').textContent = r.code;
                document.getElementById('payment-date').textContent = new Date().toLocaleDateString('fr-FR');

                const start = new Date(r.start_time.replace(" ", "T"));
                const end = new Date(r.end_time.replace(" ", "T"));

                const receiptHTML = `
                    <div class="d-flex justify-content-between mb-2"><span>Référence:</span><span>${r.code}</span></div>                    
                    <div class="d-flex justify-content-between mb-2"><span>Montant à payer:</span><span>${Number(r.payment_amount).toLocaleString('fr-FR')} XOF</span></div>
                    <div class="d-flex justify-content-between border-top pt-2 mb-2"><span>Statut:</span><span class="badge bg-danger"> En attente de paiement</span></div>
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
                    </div>
                `;
                document.getElementById('final-receipt').innerHTML = receiptHTML;
            }
            // Exécuter à l'ouverture
            generateReceipt();
        });

        // Fonction TouchPay
        reservationDataUpdated = {};

        function parseFrenchDate(dateStr) {
            // Supprimer le " à "
            const [datePart, timePart] = dateStr.split(' à ');
            const [day, month, year] = datePart.split('/');
            const [hour, minute] = timePart.split(':');

            // Créer un objet Date au format ISO
            return new Date(`${year}-${month}-${day}T${hour}:${minute}:00`);
        }


        function calltouchpay() {
            const order_number = reservationDataUpdated.reservation.code;
            const agency_code = "JSBEY11380";
            const secure_code = "UYnhBAw9f0A5DshXN8MKA6dg2VZSGs35VrXjETMZSGbJhGlhtw";
            const domain_name = 'jsbeyci.com';
            const url_redirection_success = urlWaiting.replace(':reservation_uuid', reservationUuid);
            const url_redirection_failed = urlFailed.replace(':reservation_uuid', reservationUuid);
            const amount = parseFloat(reservationDataUpdated.reservation.payment_amount);
            const city = "";
            const email = reservationDataUpdated.reservation.email || "";
            const clientFirstname = reservationDataUpdated.reservation.prenoms || "";
            const clientLastname = reservationDataUpdated.reservation.nom || "";
            const clientPhone = reservationDataUpdated.reservation.phone || "";

            sendPaymentInfos(
                order_number,
                agency_code,
                secure_code,
                domain_name,
                url_redirection_success,
                url_redirection_failed,
                amount,
                city,
                email,
                clientFirstname,
                clientLastname,
                clientPhone
            );
        }

        async function processPayment() {
            Swal.fire({
                title: 'Traitement du paiement...',
                text: 'Veuillez patienter',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            
            const payload = {
                ...reservationData
            };

            try {
                const res = await fetch('/api/reservation/update-by-paiement/' + reservationUuid, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();

                if (!data.success) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: data.message,
                    });
                    return;
                }

                // Mise à jour de reservationData avec la réponse du backend
                // reservationData = data.data;
                reservationDataUpdated.reservation = data.reservation;
                // Récupérer la date actuelle
                const now = new Date();
                const dateLimit = parseFrenchDate(dateLimitTotalStr);
                // alert(now);
                // alert(dateLimit);
                if (now > dateLimit) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Le paiement a expiré.',
                    });
                    return;
                }else{
                    // Appel TouchPay avec les infos mises à jour
                    calltouchpay();
                }
                

            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur réseau',
                    text: 'Impossible de traiter le paiement. Veuillez réessayer.'
                });
            }
        }
    </script>


    {{-- <script>
    document.addEventListener('DOMContentLoaded', function() {
            // Récupération des coordonnées depuis les variables Blade (Laravel)
            const latitude = @json($appart->property->latitude);
            const longitude = @json($appart->property->longitude);


            // Initialisation de la carte
            const map = L.map('map-location-property').setView([latitude, longitude], 16);

            // Chargement des tuiles OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            }).addTo(map);

            // Ajout d’un marqueur à l’emplacement
            L.marker([latitude, longitude]).addTo(map)
                .bindPopup("Emplacement de la propriété")
                .openPopup();
        });
</script> --}}
@endsection
