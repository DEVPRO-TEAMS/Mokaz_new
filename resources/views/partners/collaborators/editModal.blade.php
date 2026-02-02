

<!-- Modal -->
<div class="modal fade" id="editModal{{ $item->uuid }}" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title text-light" id="editModalLabel">Modifier le collaborateur</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>
        <form method="POST" action="{{ route('partner.collaborator.update', $item->uuid) }}" class="modal-content submitForm">
            @csrf
            
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-12 p-3">
                        <fieldset class="row border border-2 rounded p-3 position-relative mb-3">
                            <legend class="float-none w-auto px-2 text-primary fw-bold" style="font-size: 0.9rem;">
                                <small>Collaborateur</small>
                            </legend>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" name="name" value="{{ $item->name }}" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Prénoms <span class="text-danger">*</span></label>
                                <input type="text" name="lastname" value="{{ $item->lastname }}" class="form-control" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Téléphone</label>
                                <input type="text" name="phone" value="{{ $item->phone }}" class="form-control" required>
                            </div>
                        </fieldset>

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