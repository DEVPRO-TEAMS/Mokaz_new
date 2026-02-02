@if ($reservations->count())
    <div class="table-responsive mt-3">
        <table class="table table-striped table-hover align-middle mb-0" id="example2">
            <thead class="table-light">
                <tr>
                    <th>Code</th>
                    <th>Client</th>
                    <th>Hébergement</th>
                    <th>Dates</th>
                    <th>Prix total</th>
                    <th>Paiement</th>
                    <th>Statut</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reservations as $reservation)
                    <tr>
                        <td>{{ $reservation->code ?? 'N/A' }}</td>
                        <td>{{ $reservation->nom }} {{ $reservation->prenoms }}</td>
                        <td>{{ $reservation->appartement->title ?? 'Non lié' }}</td>
                        <td>
                            {{ \Carbon\Carbon::parse($reservation->start_time)->format('d/m/Y') }}
                            au
                            {{ \Carbon\Carbon::parse($reservation->end_time)->format('d/m/Y') }}
                        </td>
                        <td>{{ number_format($reservation->total_price, 0, ',', ' ') }} FCFA</td>
                        <td>
                            @if($reservation->statut_paiement === 'paid')
                                <span class="badge bg-success">Payé</span>
                            @else
                                <span class="badge bg-warning text-dark">En attente</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $reservation->status === 'confirmed' ? 'primary' : ($reservation->status === 'cancelled' ? 'danger' : 'secondary') }}">
                                {{ ucfirst($reservation->status) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" data-bs-target="#receiptModal{{ $reservation->uuid }}">
                                <i class="fas fa-receipt"></i> Voir le reçu
                            </button>
                        </td>
                    </tr>

                    @include('components.receip')

                @endforeach
            </tbody>
        </table>
    </div>
@else
    <p class="text-muted mt-3">Aucune réservation enregistrée.</p>
@endif
