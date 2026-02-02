@if ($users->count())
    <div class="table-responsive mt-3">
        <table class="table table-striped table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Type</th>
                    <th>Dernière connexion</th>
                    <th>Statut</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name }} {{ $user->lastname }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone }}</td>
                        <td><span class="badge bg-primary text-uppercase">{{ $user->user_type }}</span></td>
                        <td>{{ $user->last_login ? \Carbon\Carbon::parse($user->last_login)->format('d/m/Y H:i') : 'Jamais' }}
                        </td>
                        <td>
                            @if ($user->etat === 'actif')
                                <span class="badge bg-success">Actif</span>
                            @else
                                <span class="badge bg-danger">Inactif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->uuid }}"
                                    title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="Supprimer">
                                    <a class="deleteConfirmation" data-uuid="{{ $user->uuid }}"
                                        data-token="{{ csrf_token() }}"
                                        data-type="confirmation_redirect"
                                        data-url="{{ route('user.destroy', $user->uuid) }}"
                                        data-title="Vous êtes sur le point de supprimer {{ $user->name ?? ''}} {{ $user->lastname ?? ''}}"
                                        data-id="{{ $user->uuid }}"
                                        data-route="{{ route('user.destroy', $user->uuid) }}">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @include('users.components.editModal', ['user' => $user])
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <p class="text-muted mt-3">Aucun utilisateur lié à ce partenaire.</p>
@endif
