<!-- Modal détails propriété -->
<div class="modal fade" id="propertyCategoriziedModal{{ $property->uuid }}" tabindex="-1"
    aria-labelledby="propertyCategoriziedModalLabel{{ $property->uuid }}" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary bg-opacity-10 border-0">
                <div class="d-flex align-items-center">
                    <div class="property-image me-3">
                        @if ($property->image)
                            <img src="{{ asset($property->image) }}" alt="{{ $property->title }}" class="rounded-2"
                                style="width: 40px; height: 40px; object-fit: cover;">
                        @else
                            <div class="avatar-initials bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center rounded-2"
                                style="width: 40px; height: 40px;">
                                <i class="fas fa-home"></i>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="propertyCategoriziedModalLabel{{ $property->uuid }}">
                            {{ $property->title ?? 'Propriété' }}
                        </h5>
                        <small class="text-muted">Code: #{{ $property->code ?? '' }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <form action="{{ route('admin.approveProperty',$property->uuid) }}" method="post" class="submitForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <label for="category_uuid" class="form-label">Catégoriser la propriété</label>
                            <select name="category_uuid" class="form-select nice-select" id="category_uuid" required>
                                <option value="">Choisir une categorie de propriété</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->uuid }}" {{ $property->category_uuid == $category->uuid ? 'selected' : '' }}>{{ $category->libelle }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                </div>

                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Fermer
                    </button>
                    <button type="submit" class="btn btn-success me-2 text-white">
                        <i class="fas fa-check me-1"></i> Enregistrer
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
