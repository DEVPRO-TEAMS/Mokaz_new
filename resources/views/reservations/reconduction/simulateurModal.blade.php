{{-- ================= MODAL RECONDUCTION ================= --}}
<div class="modal fade" id="modalReconduction" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Simulation de reconduction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="row">
                    {{-- IMAGE --}}
                    <div class="col-md-5">
                        <img id="modalImage" class="img-fluid rounded" />
                    </div>

                    {{-- DÉTAILS --}}
                    <div class="col-md-7">
                        <h6 id="modalTitle"></h6>

                        <ul class="list-group list-group-flush mt-3">
                            <li class="list-group-item">
                                <strong>Prix du nouveau bien :</strong>
                                <span id="prixNouveau"></span> FCFA
                            </li>

                            <li class="list-group-item">
                                <strong>Réservation (10 %) :</strong>
                                <span id="reservationNouveau"></span> FCFA
                            </li>

                            <li class="list-group-item">
                                <strong>Déjà payé :</strong>
                                <span class="text-success">
                                    {{ number_format($reservation->payment_amount, 0, ',', ' ') }} FCFA
                                </span>
                            </li>

                            <li class="list-group-item">
                                <strong>Reste à payer :</strong>
                                <span id="restePayer" class="fw-bold"></span> FCFA
                            </li>
                        </ul>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button id="btnAction" onclick="onReconducer()" class="btn"></button>
            </div>

        </div>
    </div>
</div>

{{-- @include('reservations.reconduction.simulateurModal', ['appart' => $reservation->appartement]) --}}
@include('reservations.reconduction.reserRecondModal')



<script>
document.querySelectorAll('.btn-reconduire').forEach(btn => {
    btn.addEventListener('click', function () {

        const prixNouveau = parseInt(this.dataset.price);
        const ancienPaye = {{ $reservation->payment_amount }};
        const reservationNouveau = Math.round(prixNouveau * 0.10);
        const restePayer = Math.max(0, reservationNouveau - ancienPaye);

        // Injection données
        document.getElementById('modalTitle').innerText = this.dataset.appart;
        document.getElementById('modalImage').src = this.dataset.image;
        document.getElementById('prixNouveau').innerText = prixNouveau.toLocaleString();
        document.getElementById('reservationNouveau').innerText = reservationNouveau.toLocaleString();
        document.getElementById('restePayer').innerText = restePayer.toLocaleString();

        const btnAction = document.getElementById('btnAction');

        // LOGIQUE BOUTON
        if (restePayer > 0) {
            btnAction.className = 'btn btn-primary';
            btnAction.innerText = 'Procéder au paiement';
        } else {
            btnAction.className = 'btn btn-success';
            btnAction.innerText = 'Réserver ce bien';
        }

        // OUVERTURE MODAL
        new bootstrap.Modal(document.getElementById('modalReconduction')).show();
    });
});
</script>

<script>
    onReconducer = () => {
        const btnAction = document.getElementById('btnAction');
        btnAction.innerText = "Traitement en cours...";
        // alert("Reconduction en cours...");
    }
</script>



{{-- COL 4 : DÉTAILS DE LA RÉSERVATION --}}
        {{-- ==================================== --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>
                        Réservation actuelle
                    </h5>
                </div>

                <div class="card-body">
                    {{-- IMAGE DE L'APPART ACTUEL --}}
                    @if($reservationOld->appartement->image ?? false)
                        <div class="mb-4">
                            <img src="{{ asset($reservationOld->appartement->image) }}" 
                                 class="img-fluid rounded" 
                                 alt="Appartement actuel"
                                 style="height: 200px; width: 100%; object-fit: cover;">
                        </div>
                    @endif

                    {{-- DÉTAILS DE LA RÉSERVATION --}}
                    <h6 class="fw-bold">{{ $reservationOld->appartement->title ?? 'Non spécifié' }}</h6>
                    
                    <div class="mt-4">
                        <div class="mb-3">
                            <small class="text-muted">Période de réservation</small>
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Entrée :</span>
                                <strong>{{ \Carbon\Carbon::parse($reservationOld->date_in ?? now())->format('d/m/Y') }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Sortie :</span>
                                <strong>{{ \Carbon\Carbon::parse($reservationOld->date_out ?? now())->format('d/m/Y') }}</strong>
                            </div>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">Détails financiers</small>
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Prix total :</span>
                                <strong>{{ number_format($reservationOld->total_price ?? 0, 0, ',', ' ') }} FCFA</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Acompte (10%) :</span>
                                <span class="text-success fw-bold">
                                    {{ number_format($reservationOld->payment_amount ?? 0, 0, ',', ' ') }} FCFA
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Reste à payer :</span>
                                <span class="text-danger fw-bold">
                                    {{ number_format(($reservationOld->total_price ?? 0) - ($reservationOld->payment_amount ?? 0), 0, ',', ' ') }} FCFA
                                </span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <small class="text-muted">Statut du paiement</small>
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Statut :</span>
                                @if(($reservationOld->statut_paiement ?? '') === 'paid')
                                    <span class="badge bg-success rounded-pill">
                                        <i class="fas fa-check-circle me-1"></i> Payé
                                    </span>
                                @else
                                    <span class="badge bg-warning rounded-pill">
                                        <i class="fas fa-clock me-1"></i> En attente
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- NOTE IMPORTANTE --}}
                        <div class="alert alert-warning small mb-0">
                            <div class="d-flex">
                                <i class="fas fa-exclamation-circle me-2 mt-1"></i>
                                <div>
                                    <strong>Important :</strong> Vous pouvez changer d'appartement uniquement si 
                                    le nouveau bien a un prix <strong>égal ou supérieur</strong> au prix actuel.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>