@if ($units->count())
    <div class="table-responsive mt-3">
        <table class="table table-striped table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Code</th>
                    <th>Titre</th>
                    <th>Propriété liée</th>
                    <th>Nombre de chambres</th>
                    <th>Salle de bain</th>
                    <th>Disponibles</th>
                    <th>Commodité</th>
                    <th>État</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($units as $unit)
                    <tr>
                        <td>{{ $unit->code ?? 'N/A' }}</td>
                        <td>{{ $unit->title ?? '-' }}</td>
                        <td>{{ $unit->property->title ?? 'Non lié' }}</td>
                        <td>{{ $unit->nbr_room ?? 0 }}</td>
                        <td>{{ $unit->nbr_bathroom ?? 0 }}</td>
                        <td>{{ $unit->nbr_available ?? 0 }}</td>
                        <td>{{ $unit->commodity->name ?? '-' }}</td>
                        <td>
                            @if ($unit->etat === 'actif')
                                <span class="badge bg-success">Actif</span>
                            @else
                                <span class="badge bg-secondary">Inactif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#showApartmentModal{{ $unit->code }}"
                                                                title="Voir détails"  class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @foreach($units as $apartement)
        @include('properties.apparts.showModal' , ['apartement' => $apartement])
    @endforeach
@else
    <p class="text-muted mt-3">Aucune unité enregistrée.</p>
@endif
