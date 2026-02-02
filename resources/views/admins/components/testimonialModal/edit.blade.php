<!-- Modal d'ajout -->
<div class="modal fade" id="editTestimonialModal{{ $item->uuid }}" tabindex="-1" aria-labelledby="editTestimonialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-light" id="editTestimonialModalLabel">
                    <i class="bi bi-plus-circle me-2 text-light"></i>Modifier le Témoignage
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form  method="POST" action="{{ route('admin.update.testimonial', $item->uuid) }}" class="submitForm">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-sm-12 col-md-12">
                            <label for="add_name" class="form-label">Nom complet du témoin <span class="text-danger">*</span></label>
                            <input type="text"  id="add_name" name="name" value="{{ $item->name }}" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12">
                            <label for="add_description" class="form-label">Témoignage</label>
                            <textarea class="form-control" id="add_content" name="content" rows="3" maxlength="700">{{ $item->content }}</textarea>
                            <div class="form-text">Maximum 700 caractères</div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">
                        <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
                        <i class="bi bi-check-circle me-2"></i>Modifier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>