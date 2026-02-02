<!-- Modal d'ajout -->
<div class="modal fade" id="editVariableModal{{ $item->uuid }}" tabindex="-1" aria-labelledby="addVariableModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-light" id="addVariableModalLabel">
                    <i class="bi bi-plus-circle me-2 text-light"></i>Modifier la Variable
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('setting.updateVariable', $item->uuid) }}" class="submitForm">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-sm-12 col-md-12">
                            <label for="add_libelle" class="form-label">Libellé <span
                                    class="text-danger">*</span></label>
                            @if ($isPropertyPage)
                                <input list="types_propriete" type="text" class="form-control" id="add_libelle"
                                    placeholder="Sélectionnez ou saisissez le libelle" name="libelle"
                                    value="{{ $item->libelle }}" required>
                                <datalist id="types_propriete">
                                    <option value="Résidence privée">
                                    <option value="Immeuble">
                                    <option value="Villa">
                                    <option value="Studio indépendant">
                                    <option value="Auberge">
                                    <option value="Hôtel">
                                    <option value="Guest house / Maison d’hôte">
                                    <option value="Chalet">
                                    <option value="Bungalow">
                                    <option value="Maison mobile / Conteneur">
                                    <option value="Résidence hôtelière">
                                </datalist>
                            @elseif ($isAppartPage)
                                <input list="types_appartement" name="libelle" class="form-control" required
                                    id="add_libelle" placeholder="Sélectionnez ou saisissez le libelle"
                                    value="{{ $item->libelle }}">
                                <datalist id="types_appartement">
                                    <option value="Studio">
                                    <option value="Appartement F1">
                                    <option value="Appartement F2">
                                    <option value="Appartement F3">
                                    <option value="Appartement F4">
                                    <option value="Duplex">
                                    <option value="Loft">
                                    <option value="Penthouse">
                                    <option value="Mini studio">
                                    <option value="Suite">
                                    <option value="Colocation">
                                    <option value="Dortoir">
                                </datalist>
                            @elseif ($isCategoryPage)
                                <input list="categories_bien" name="libelle" class="form-control" required
                                    id="add_libelle" placeholder="Sélectionnez ou saisissez le libelle"
                                    value="{{ $item->libelle }}">
                                <datalist id="categories_bien">
                                    <option value="Location meublée">
                                    <option value="Location non meublée">
                                    <option value="Courte durée">
                                    <option value="Longue durée">
                                    <option value="Résidentiel">
                                    <option value="Commercial">
                                    <option value="Luxe">
                                    <option value="Économique">
                                    <option value="Famille">
                                    <option value="Vacances">
                                    <option value="Étudiant">
                                </datalist>
                            @endif
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-12">
                            <label for="add_type" class="form-label">Type <span class="text-danger">*</span></label>
                            <select class="form-select nice-select" id="add_type" name="type" required>
                                <option value="" disabled {{ $item->type == null ? 'selected' : '' }}>Sélectionner
                                    un type</option>

                                <option value="type_of_property"
                                    {{ $item->type == 'type_of_property' ? 'selected' : '' }}
                                    {{ $isPropertyPage ? '' : 'disabled' }}>
                                    Type de bien
                                </option>

                                <option value="type_of_appart" {{ $item->type == 'type_of_appart' ? 'selected' : '' }}
                                    {{ $isAppartPage ? '' : 'disabled' }}>
                                    Type d'appart
                                </option>
                                <option value="category_of_property"
                                    {{ $item->type == 'category_of_property' ? 'selected' : '' }}
                                    {{ $isCategoryPage ? '' : 'disabled' }}>
                                    Cotégorie de bien
                                </option>

                                <option value="autre" {{ $item->type == 'autre' ? 'selected' : '' }}
                                    {{ $isAppartPage || $isPropertyPage || $isCategoryPage ? 'disabled' : '' }}>
                                    Autre
                                </option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12">
                            <label for="add_description" class="form-label">Description</label>
                            <textarea class="form-control" id="add_description" name="description" value="{{ $item->description }}"
                                rows="3" maxlength="500"></textarea>
                            <div class="form-text">Maximum 500 caractères</div>
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
