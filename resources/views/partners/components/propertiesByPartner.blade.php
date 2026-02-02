@if ($properties->count())
    <div class="table-responsive mt-3">
        <table class="table table-striped table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Code</th>
                    <th>Propriété</th>
                    <th>Localisation</th>
                    <th>Pays</th>
                    <th>Partenaire</th>
                    <th>Créée le</th>
                    <th>État</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($properties as $property)
                    <tr>
                        <td>{{ $property->code ?? 'N/A' }}</td>
                        <td>{{ $property->title ?? '-' }}</td>
                        <td>{{ $property->address }}, {{ $property->city }}</td>
                        <td>{{ $property->country ?? '-' }}</td>
                        <td>{{ $property->partner->raison_social ?? 'Partenaire inconnu' }}</td>
                        <td>{{ $property->created_at ? $property->created_at->format('d/m/Y') : '-' }}</td>
                        <td>
                            @if ($property->etat == 'actif')
                                <span class="badge bg-success">Actif</span>
                            @else
                                <span class="badge bg-secondary">Inactif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.properties.show', $property->uuid) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <p class="text-muted mt-3">Aucune propriété liée à ce partenaire.</p>
@endif
