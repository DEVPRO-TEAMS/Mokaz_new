<div class="modal fade" id="addCollaboratorModal" tabindex="-1" aria-labelledby="addCollaboratorModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('partner.collaborator.store') }}" class="modal-content submitForm">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title text-light" id="addCollaboratorModalLabel">Ajouter un collaborateur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    
                    <div class="col-md-12 p-3">
                        <fieldset class="row border border-2 rounded p-3 position-relative mb-3">
                            <legend class="float-none w-auto px-2 text-primary fw-bold" style="font-size: 0.9rem;">
                                <small>Collaborateur</small>
                            </legend>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" name="contact_name" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Prénoms <span class="text-danger">*</span></label>
                                <input type="text" name="contact_lastname" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="contact_email" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Téléphone</label>
                                <input type="text" name="phone" class="form-control" required>
                            </div>
                        </fieldset>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-success">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
