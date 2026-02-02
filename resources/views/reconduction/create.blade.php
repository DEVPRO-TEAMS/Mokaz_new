@extends('layouts.main')
@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- En-tête -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">Reconduire ma réservation</h4>
                            <p class="text-muted mb-0">Réservation #{{ $reservation->code }}</p>
                        </div>
                        <a href="{{ route('reservation.show', $reservation->uuid) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
            </div>

            <!-- Information actuelle -->
            <div class="card border-primary border-2 mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-home me-2"></i>Bien actuel</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <img src="{{ asset($reservation->appartement->image) }}" alt="" class="img-fluid rounded">
                        </div>
                        <div class="col-md-8">
                            <h5>{{ $reservation->appartement->title }}</h5>
                            <p class="text-muted">{{ $reservation->appartement->property->address }}</p>
                            
                            <div class="row mt-3">
                                <div class="col-6">
                                    <small class="text-muted">Prix total</small>
                                    <h5 class="text-primary">{{ number_format($reservation->total_price, 0, ',', ' ') }} XOF</h5>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">10% déjà payés</small>
                                    <h5 class="text-success">{{ number_format($reservation->payment_amount, 0, ',', ' ') }} XOF</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Biens éligibles -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-exchange-alt me-2"></i>Choisir un nouveau bien</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        Vous ne pouvez choisir que les biens dont le prix est supérieur ou égal à votre bien actuel.
                    </p>

                    @if($eligibleAppartements->isEmpty())
                        <div class="alert alert-warning">
                            Aucun bien éligible trouvé pour le moment.
                        </div>
                    @else
                        <div class="row" id="eligible-appartements">
                            @foreach($eligibleAppartements as $appart)
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100 border 
                                        {{ $appart->is_available ? 'border-success' : 'border-danger' }} 
                                        appartement-card"
                                        data-appart-uuid="{{ $appart->uuid }}"
                                        data-price="{{ $appart->property->price }}">
                                        @if(!$appart->is_available)
                                            <div class="card-header bg-danger text-white py-1">
                                                <small>Non disponible pour cette période</small>
                                            </div>
                                        @endif
                                        <img src="{{ asset($appart->image) }}" class="card-img-top" alt="{{ $appart->title }}">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $appart->title }}</h6>
                                            <p class="card-text text-muted small">
                                                {{ Str::limit($appart->description, 100) }}
                                            </p>
                                            
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h5 class="text-primary mb-0">{{ number_format($appart->property->price, 0, ',', ' ') }} XOF</h5>
                                                    <small class="text-muted">Prix total</small>
                                                </div>
                                                
                                                <button class="btn btn-sm btn-outline-primary select-appartement"
                                                    {{ !$appart->is_available ? 'disabled' : '' }}>
                                                    <i class="fas fa-check"></i> Choisir
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-light">
                                            <small class="text-muted">
                                                10% requis: <strong>{{ number_format($appart->property->ten_percent, 0, ',', ' ') }} XOF</strong><br>
                                                Reste à payer: <strong class="text-warning">{{ number_format($appart->property->remaining_to_pay, 0, ',', ' ') }} XOF</strong>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Calcul détaillé (caché par défaut) -->
                        <div class="card mt-4 d-none" id="calculation-card">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Détail du calcul</h5>
                            </div>
                            <div class="card-body">
                                <div id="calculation-details">
                                    <!-- Rempli dynamiquement -->
                                </div>
                                
                                <form action="{{ route('reconduction.initiate') }}" method="POST" id="reconduction-form">
                                    @csrf
                                    <input type="hidden" name="reservation_uuid" value="{{ $reservation->uuid }}">
                                    <input type="hidden" name="new_appart_uuid" id="new_appart_uuid">
                                    
                                    <div class="form-check mt-3">
                                        <input class="form-check-input" type="checkbox" name="accept_terms" id="accept_terms" required>
                                        <label class="form-check-label" for="accept_terms">
                                            J'accepte les <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">conditions de reconduction</a>
                                        </label>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mt-4">
                                        <button type="button" class="btn btn-secondary" id="cancel-selection">
                                            <i class="fas fa-times me-2"></i>Annuler
                                        </button>
                                        <button type="submit" class="btn btn-primary" id="submit-btn" disabled>
                                            <i class="fas fa-arrow-right me-2"></i>Procéder au paiement
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Conditions -->
<div class="modal fade" id="termsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Conditions de reconduction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>En procédant à la reconduction :</p>
                <ol>
                    <li>Votre réservation actuelle sera automatiquement annulée</li>
                    <li>Les 10% déjà payés seront transférés vers la nouvelle réservation</li>
                    <li>Si le nouveau bien est plus cher, vous paierez la différence des 10%</li>
                    <li>Aucun remboursement ne sera effectué si le nouveau bien est moins cher</li>
                    <li>La disponibilité du nouveau bien n'est garantie qu'après paiement complet</li>
                </ol>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectedAppartement = null;
    
    // Sélection d'un appartement
    document.querySelectorAll('.select-appartement').forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.appartement-card');
            const uuid = card.dataset.appartUuid;
            const price = parseFloat(card.dataset.price);
            
            // Mettre en évidence la sélection
            document.querySelectorAll('.appartement-card').forEach(c => {
                c.classList.remove('border-primary', 'border-3');
            });
            card.classList.add('border-primary', 'border-3');
            
            selectedAppartement = { uuid, price };
            
            // Afficher le calcul
            calculateReconduction(uuid);
        });
    });
    
    function calculateReconduction(newAppartUuid) {
        const reservationUuid = '{{ $reservation->uuid }}';
        
        fetch('{{ route("reconduction.calculate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                reservation_uuid: reservationUuid,
                new_appart_uuid: newAppartUuid
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayCalculation(data.calculation);
                document.getElementById('new_appart_uuid').value = newAppartUuid;
                document.getElementById('calculation-card').classList.remove('d-none');
                
                // Scroll vers le calcul
                document.getElementById('calculation-card').scrollIntoView({ behavior: 'smooth' });
            }
        })
        .catch(error => console.error('Error:', error));
    }
    
    function displayCalculation(calc) {
        const container = document.getElementById('calculation-details');
        
        const html = `
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <small class="text-muted">Ancien prix total</small>
                        <h5>${formatNumber(calc.old_total_price)} XOF</h5>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">10% de l'ancien bien (déjà payé)</small>
                        <h5 class="text-success">${formatNumber(calc.already_paid)} XOF</h5>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <small class="text-muted">Nouveau prix total</small>
                        <h5>${formatNumber(calc.new_total_price)} XOF</h5>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">10% du nouveau bien requis</small>
                        <h5>${formatNumber(calc.new_ten_percent)} XOF</h5>
                    </div>
                </div>
            </div>
            
            <div class="alert ${calc.remaining_to_pay > 0 ? 'alert-warning' : 'alert-success'}">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">${calc.remaining_to_pay > 0 ? 'Montant à régler' : 'Aucun supplément à payer'}</h5>
                        <p class="mb-0">
                            ${calc.remaining_to_pay > 0 ? 
                                `Vous avez déjà payé ${formatNumber(calc.already_paid)} XOF. 
                                Il reste ${formatNumber(calc.remaining_to_pay)} XOF à payer.` :
                                'Votre paiement initial couvre les 10% du nouveau bien.'
                            }
                        </p>
                    </div>
                    ${calc.remaining_to_pay > 0 ? 
                        `<h3 class="text-warning mb-0">${formatNumber(calc.remaining_to_pay)} XOF</h3>` :
                        `<h3 class="text-success mb-0"><i class="fas fa-check-circle"></i></h3>`
                    }
                </div>
            </div>
        `;
        
        container.innerHTML = html;
    }
    
    function formatNumber(num) {
        return new Intl.NumberFormat('fr-FR').format(num);
    }
    
    // Annuler la sélection
    document.getElementById('cancel-selection').addEventListener('click', function() {
        selectedAppartement = null;
        document.querySelectorAll('.appartement-card').forEach(c => {
            c.classList.remove('border-primary', 'border-3');
        });
        document.getElementById('calculation-card').classList.add('d-none');
        document.getElementById('submit-btn').disabled = true;
    });
    
    // Activer/désactiver le bouton de soumission
    document.getElementById('accept_terms').addEventListener('change', function() {
        document.getElementById('submit-btn').disabled = !this.checked;
    });
});
</script>
@endpush