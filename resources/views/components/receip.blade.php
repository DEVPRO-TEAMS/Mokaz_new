{{-- Modal reçu --}}
<div class="modal fade" id="receiptModal{{ $reservation->uuid }}" tabindex="-1" aria-labelledby="receiptModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="receiptModalLabel">Reçu de Réservation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-5 py-4">
                    {{-- <h4 class="mb-3">{{ $reservation->nom }} {{ $reservation->prenoms }}</h4>
                    <p><strong>Email :</strong> {{ $reservation->email }}</p>
                    <p><strong>Téléphone :</strong> {{ $reservation->phone }}</p>

                    <hr>

                    <p><strong>Appartement :</strong> {{ $reservation->appartement->title ?? 'N/A' }}</p>
                    <p><strong>Durée du séjour :</strong> {{ $reservation->nbr_of_sejour }} nuit(s)</p>
                    <p><strong>Du :</strong> {{ \Carbon\Carbon::parse($reservation->start_time)->format('d/m/Y') }}</p>
                    <p><strong>Au :</strong> {{ \Carbon\Carbon::parse($reservation->end_time)->format('d/m/Y') }}</p>

                    <hr>

                    <p><strong>Prix unitaire :</strong> {{ number_format($reservation->unit_price, 0, ',', ' ') }} FCFA</p>
                    <p><strong>Total à payer :</strong> <span class="fw-bold text-danger">{{ number_format($reservation->total_price, 0, ',', ' ') }} FCFA</span></p>
                    <p><strong>Statut du paiement :</strong>
                        @if ($reservation->statut_paiement === 'paid')
                            <span class="badge bg-success">Payé</span>
                        @else
                            <span class="badge bg-warning text-dark">En attente</span>
                        @endif
                    </p>

                    @if ($reservation->notes)
                        <hr>
                        <p><strong>Notes :</strong><br>{{ $reservation->notes }}</p>
                    @endif --}}

                    <div class="d-flex justify-content-center">
                        <iframe src="{{ asset($reservation->receipt->filepath) }}" frameborder="0" style="width: 100%; height: 500px;"></iframe>
                        {{-- <img src="{{ asset($reservation->receipt->filepath) }}" alt="Reçu de Réservation" class="img-fluid"> --}}
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    {{-- Bouton d’impression future ? --}}
                    {{-- <button class="btn btn-outline-primary">Imprimer</button> --}}
                </div>
            </div>
        </div>
    </div>