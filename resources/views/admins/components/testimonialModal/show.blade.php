<!-- MODAL Bootstrap simplifiée -->
<div class="modal fade" id="showTestimonialModal{{ $item->uuid }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <!-- Header -->
            <div class="modal-header bg-light">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <!-- Body -->
            <div class="modal-body">
                <div class="pt-4">
                    <h6 class="text-danger"><i class="fas fa-user"></i> Nom du témoin </h6>
                    <div class="py-4 bg-white border border-danger rounded-3 p-3">
                        <p>{{ $item->name ?? '' }}</p>
                    </div>
                </div>
                <!-- Témoignage -->
                <div class="pt-4">
                    <h6 class="text-danger"><i class="bi bi-align-start"></i> Témoignage</h6>
                    <div class="bg-white border border-danger rounded-3 p-3">
                        <p class="text-muted">
                            {!! $item->content ?? '' !!}
                        </p>
                    </div>
                </div>

            </div>
            <!-- Footer -->
            <div class="modal-footer btn-container">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>

        </div>
    </div>
</div>
