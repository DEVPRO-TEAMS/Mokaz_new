

<!-- Modal -->
<div class="modal fade" id="editUserModal{{ $user->uuid }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title text-light" id="editUserModalLabel">Modifier l'utilisateur</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>
        <form method="POST" action="{{ route('admin.user.update', $user->uuid) }}" class="modal-content submitForm">
            @csrf
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Prénoms <span class="text-danger">*</span></label>
                        <input type="text" name="lastname" class="form-control" value="{{ $user->lastname }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Téléphone</label>
                        <input type="text" name="phone" class="form-control" value="{{ $user->phone }}">
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-success">Modifier</button>
            </div>
        </form>
    </div>
  </div>
</div>