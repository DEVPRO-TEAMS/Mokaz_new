

<!-- Modal -->
<div class="modal fade" id="editModal{{ $partner->uuid }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title text-light" id="editPartnerModalLabel">Modifier le partenaire</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>
        <form method="POST" action="{{ route('admin.updatePartner', $partner->uuid) }}" class="modal-content submitForm">
            @csrf
            
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Raison sociale *</label>
                        <input type="text" name="raison_social" value="{{ $partner->raison_social }}" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $partner->email }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Téléphone</label>
                        <input type="text" name="phone" class="form-control" value="{{ $partner->phone }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Site Web</label>
                        <input type="text" name="website" class="form-control" value="{{ $partner->website }}">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Adresse</label>
                        <textarea name="adresse" class="form-control" rows="2" value="{{ $partner->adresse }}">{{ $partner->adresse }}</textarea>
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