@extends('layouts.app')

@section('content')
    <style>
        /* Styles pour la liste des fichiers */
        .files-list {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
            display: none;
        }

        .files-list.show {
            display: block;
        }

        .files-list h6 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 14px;
            font-weight: 600;
        }

        .file-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 12px;
            margin: 5px 0;
            background: white;
            border-radius: 4px;
            border: 1px solid #e0e0e0;
        }

        .file-info {
            display: flex;
            align-items: center;
            flex: 1;
        }

        .file-icon {
            margin-right: 8px;
            font-size: 16px;
        }

        .file-details {
            display: flex;
            flex-direction: column;
        }

        .file-name-display {
            font-size: 13px;
            color: #333;
            font-weight: 500;
        }

        .file-size {
            font-size: 11px;
            color: #666;
        }

        .file-remove {
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 3px;
            padding: 4px 8px;
            font-size: 11px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .file-remove:hover {
            background: #c82333;
        }

        .files-count {
            color: #007bff;
            font-weight: 600;
        }

        .preview-container img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-bottom: 10px;
            border-radius: 8px;
        }

        .preview-item {
            position: relative;
            display: inline-block;
        }

        .remove-btn {
            position: absolute;
            top: -8px;
            right: -8px;
            background: red;
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            cursor: pointer;
            font-size: 12px;
        }

        #selectedCommodities {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .commodity-badge {
            background-color: #f1f1f1;
            color: #333;
            padding: 6px 12px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            font-size: 14px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .commodity-badge .remove-commodities {
            margin-left: 8px;
            font-weight: bold;
            color: #dc3545;
            cursor: pointer;
            transition: color 0.3s;
        }

        .commodity-badge .remove-commodities:hover {
            color: #a71d2a;
        }
    </style>
    <div class="main-content-inne pt-5 mt-5 wrap-dashboard-content">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">
                        <i class="fas fa-home me-2 text-danger"></i> Modifier l'appart #{{ $appart->code ?? '' }}
                        {{-- dans la propriété #{{$appart->property->code}} --}}
                    </h3>
                </div>
            </div>
        </div>
        <form id="editAppartForm" enctype="multipart/form-data">
            {{-- @csrf --}}
            <input type="hidden" name="user_uuid" value="{{ Auth::user()->uuid ?? '' }}">
            <input type="hidden" name="property_uuid" id="property_uuid" value="{{ $appart->property->uuid ?? '' }}">
            <input type="hidden" name="appart_uuid" id="appart_uuid" value="{{ $appart->uuid ?? '' }}">
            @php
                use Illuminate\Support\Str;
                $cheminExtrait = Str::after($appart->image, 'storage/files/');
                $imagePath = base_path(env('STORAGE_FILES') . $cheminExtrait);

                $countTarif = $appart->tarifications->count();
                $countAppartImages = $appart->images->count();

                $tarifJour = $appart->tarifications->where('sejour', 'Jour')->first();
            @endphp
            <div class="widget-box-2">
                <h6 class="title">Charger l'images de l'hébergement</h6>
                <div class="box-uploadfile text-center">
                    <label class="uploadfile">
                        <span class="icon icon-img-2"></span>
                        <div class="btn-upload">
                            <a href="#" class="tf-btn primary">Choisir l'image</a>
                            <input type="file" class="ip-file" name="image" accept="image/*" @if (!file_exists($imagePath)) required @endif>
                        </div>
                        <p class="file-name fw-5">Ou glisser déposez l'images ici</p>
                    </label>
                </div>
                <div class="pt-3 mt-3 bg-white border border-secondary rounded-3 p-3">
                    <h6>Image principale chargée</h6>
                    @if (file_exists($imagePath))
                        <div class="preview-container d-flex flex-wrap gap-3 mt-3">
                            <div class="preview-item">
                                <img src="{{ asset($appart->image) ?? '' }}" alt="">
                                {{-- <button type="button" class="remove-btn"><i class="fas fa-times"></i></button> --}}
                            </div>
                        </div>
                    @else
                        <p>Aucune image principale enregistrée.</p>
                    @endif
                </div>
            </div>
            <div class="widget-box-2">
                <h6 class="title">Information sur l'hébergement</h6>
                <div class="box-info-property">
                    <fieldset class="box box-fieldset">
                        <label for="title">
                            Titre:<span>*</span>
                        </label>
                        <input type="text" class="form-control style-1" placeholder="Entrer le titre de la propriété"
                            name="title" value="{{ $appart->title ?? '' }}" required>
                    </fieldset>
                    <fieldset class="box box-fieldset">
                        <label for="desc">Description:</label>
                        <textarea class="textarea-tinymce" name="description" placeholder="Entrer la description de la propriété"
                            cols="30" rows="10" id="desc">{{ $appart->description ?? '' }}</textarea>
                    </fieldset>
                </div>
            </div>

            <div class="widget-box-2">
                <h6 class="title">Tarifs</h6>
                <div id="tarifs-container">
                    <div class="box-price-property tarif-block mb-3">
                        <div class="row box">
                            <fieldset class="box-fieldset col-md-4">
                                <label for="sejour_en_0">
                                    Séjour en:<span>*</span>
                                </label>
                                <select class="form-select list style-1 nice-select sejour-en" name="sejour_en[]" @if ($countTarif == 0) required @endif>
                                    <option value="" disabled selected>Sélectionnez...</option>
                                    <option value="Jour">Jour</option>
                                    <option value="Heure" @if (!empty($tarifJour)) selected @endif>Heure</option>
                                </select>
                            </fieldset>
                            <fieldset class="box-fieldset col-md-4">
                                <label>
                                    Nombre <span class="temps-label"></span>:<span>*</span>
                                </label>
                                <input type="number" name="temps[]" class="form-control style-1 temps-input"
                                    placeholder="Exemple valeur: 1" @if ($countTarif == 0) required @endif>
                            </fieldset>
                            <fieldset class="box-fieldset col-md-3">
                                <label>
                                    Coût:<span>*</span>
                                </label>
                                <input type="number" name="prix[]" class="form-control style-1"
                                    placeholder="Exemple valeur: 20000" @if ($countTarif == 0) required @endif>
                            </fieldset>
                            <fieldset class="box-fieldset col-md-1 pt-md-2 pt-lg-2 remove-container">
                                {{-- Le bouton "Supprimer" sera inséré ici --}}
                            </fieldset>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="button" id="addTarifBtn" class="tf-btn primary">+ Ajouter un tarif</button>
                </div>

                <div class="d-flex justify-content-end">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Sejour en</th>
                                <th>Temps</th>
                                <th>Prix</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($appart->tarifications as $tarif)
                                <tr class="tarifs-row">
                                    <td>#{{ $tarif->code }}</td>
                                    <td>{{ $tarif->sejour }}</td>
                                    <td>{{ $tarif->nbr_of_sejour }}</td>
                                    <td>{{ $tarif->price }}</td>
                                    <td class="text-center">
                                        <input type="hidden" name="tarif_uuid" value="{{ $tarif->uuid }}">
                                        <button type="button" class="btn btn-sm btn-danger removeTarif"><i
                                                class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="widget-box-2">
                <h6 class="title">Informations complémentaires</h6>
                <div class="row box">
                    <fieldset class="box-fieldset col-md-3">
                        <label for="appartType">
                            Type de l'hébergement:<span>*</span>
                        </label>

                        <select class="form-select nice-select list style-1" id="appartType" name="type_uuid" required>
                            <option value="" disabled>-- Choisir le type d'hébergement --</option>
                            @foreach ($typeAppart as $item)
                                <option value="{{ $item->uuid }}" @if ($appart->type_uuid == $item->uuid) selected @endif>
                                    {{ $item->libelle }}</option>
                            @endforeach
                        </select>
                    </fieldset>
                    <fieldset class="box-fieldset col-md-3">
                        <label for="bedrooms">
                            Nombre de chambres:<span>*</span>
                        </label>
                        <input type="number" name="nbr_room" value="{{ $appart->nbr_room ?? '' }}"
                            class="form-control style-1" min="0" placeholder="Exemple valeur: 1" required>
                    </fieldset>
                    <fieldset class="box-fieldset col-md-3">
                        <label for="bathrooms">
                            Nombre de salles de bain:<span>*</span>
                        </label>
                        <input type="number" name="nbr_bathroom" value="{{ $appart->nbr_bathroom ?? '' }}"
                            class="form-control style-1" min="0" placeholder="Exemple valeur: 1" required>
                    </fieldset>
                    <fieldset class="box-fieldset col-md-3">
                        <label for="neighborhood">
                            Nombre disponible:<span>*</span>
                        </label>
                        <input type="number" name="nbr_available" value="{{ $appart->nbr_available ?? '' }}"
                            min="0" class="form-control style-1" placeholder="Exemple valeur: 1">
                    </fieldset>
                </div>
            </div>

            <div class="widget-box-2">
                <h6 class="title">Commodités <span>*</span></h6>
                <div class="box-amenities-proper commodities-container">
                    <div class="box-amenitie commodity-block">
                        <div class="row box">
                            <fieldset class="box-fieldset col-12">
                                <label>Ajouter une commodité:<span>*</span></label>
                                <div class="input-group">
                                    <input id="commoditiesInput" class="form-control style-1" type="text"
                                        placeholder="Entrez une commodité" list="commoditiesList">
                                    <datalist id="commoditiesList">
                                        <!-- Commodités Générales -->
                                        <option value="Climatisation">
                                        <option value="Ventilateur">
                                        <option value="Internet Wi-Fi">
                                        <option value="Télévision">
                                        <option value="Ascenseur">
                                        <option value="Sécurité 24/7">
                                        <option value="Parking">
                                        <option value="Balcon">
                                        <option value="Terrasse">
                                        <option value="Générateur de secours">
                                        <option value="Caméras de surveillance">
                                        <option value="Conciergerie">
                                        <option value="Piscine">
                                        <option value="Salle de sport">
                                        <option value="Aire de jeux pour enfants">
                                        <option value="Espace vert / Jardin">

                                            <!-- Commodités Cuisine -->
                                        <option value="Réfrigérateur">
                                        <option value="Cuisinière à gaz">
                                        <option value="Micro-ondes">
                                        <option value="Four">
                                        <option value="Cafetière">
                                        <option value="Bouilloire électrique">
                                        <option value="Ustensiles de cuisine">
                                        <option value="Évier double">
                                        <option value="Lave-vaisselle">
                                        <option value="Plans de travail en marbre">

                                            <!-- Commodités Chambre à coucher -->
                                        <option value="Lit king size">
                                        <option value="Lit queen size">
                                        <option value="Draps et couvertures">
                                        <option value="Moustiquaire">
                                        <option value="Placard intégré">
                                        <option value="Table de chevet">
                                        <option value="Bureau de travail">
                                        <option value="Rideaux occultants">

                                            <!-- Commodités Salle de bain -->
                                        <option value="Eau chaude">
                                        <option value="Douche italienne">
                                        <option value="Baignoire">
                                        <option value="Toilettes modernes">
                                        <option value="Serviettes de bain">
                                        <option value="Lavabo double vasque">
                                        <option value="Machine à laver">
                                    </datalist>
                                    <button id="addCommoditiesBtn" class="tf-btn primary" type="button">Ajouter</button>
                                </div>

                                <div id="selectedCommodities" class="mt-3 d-flex flex-wrap gap-2">
                                    <!-- Commodités existantes ici -->
                                    @if (!empty($appart->commodities))
                                        @foreach (explode(',', $appart->commodities) as $item)
                                            <div class="commodity-badge">
                                                <span>{{ trim($item) }}</span>
                                                <span class="remove-commodities" style="cursor:pointer;">X</span>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                <input type="hidden" id="hiddenCommoditiesInput" name="commodities"
                                    value="{{ $appart->commodities ?? '' }}">
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>

            <div class="widget-box-2">
                <h6 class="title">Charger les images de l'hébergement</h6>
                <div class="box-uploadfile text-center">
                    <label class="uploadfile">
                        <span class="icon icon-img-2"></span>
                        <div class="btn-upload">
                            <a href="#" class="tf-btn primary">Choisir au moins une image</a>
                            <input type="file" class="ip-file ip-files" name="images_appart[]" id="imagesInput"
                                multiple accept="image/*" @if ($countAppartImages == 0) required @endif>
                        </div>
                        <p class="file-nam fw-5">
                            Fichiers sélectionnés (<span class="files-count text-danger" id="filesCount">0</span>)
                        </p>
                    </label>
                </div>

                <div id="previewContainer" class="preview-container d-flex flex-wrap gap-3 mt-3"></div>

                <div class="pt-3 mt-3 bg-white border border-secondary rounded-3 p-3">
                    <h6>Images chargées</h6>
                    <div class="preview-container d-flex flex-wrap gap-3 mt-3">
                        @forelse($appart->images as $image)
                            <div class="preview-item img-item">
                                <img src="{{ asset($image->doc_url) ?? '' }}" alt="">
                                <input type="hidden" name="image_uuid" value="{{ $image->uuid }}">
                                <button type="button" class="remove-btn removeImage"><i
                                        class="fas fa-times"></i></button>
                            </div>
                        @empty
                            {{-- afficher un message --}}
                            <p>Aucune image enregistrée.</p>
                        @endforelse

                    </div>
                </div>
            </div>
            <div class="widget-box-2">
                <h6 class="title">Videos de l'hébergement</h6>
                <fieldset class="box-fieldset">
                    <label for="video">URL de la vidéo (Youtube de preference) :</label>
                    <input type="url" class="form-control style-1" id="video" name="video_url"
                        value="{{ $appart->video_url ?? '' }}" placeholder="Youtube de preference">
                </fieldset>
            </div>
            <div class="d-flex justify-content-end">
                <button type="submit" class="tf-btn primary"> Ajouter</button>
            </div>

        </form>

    </div>

    <script>
        document.getElementById('editAppartForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const property_uuid = document.getElementById('property_uuid').value;
            const appart_uuid = document.getElementById('appart_uuid').value;
            const textareaId = 'desc';

            // Synchroniser le contenu TinyMCE avec le textarea
            if (typeof tinymce !== 'undefined' && tinymce.get(textareaId)) {
                tinymce.triggerSave();
            }

            const content = document.getElementById(textareaId).value.trim();

            if (!content) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Champ requis',
                    text: 'Le champ de description est obligatoire.',
                    showConfirmButton: false,
                    timer: 1500,
                    progressBar: true
                });
                return;
            }

            // Validation des tarifs
            const tarifBlocks = document.querySelectorAll('.tarif-block');
            if (tarifBlocks.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tarif requis',
                    text: 'Au moins un tarif doit être défini.',
                    showConfirmButton: false,
                    progressBar: true,
                    timer: 1500
                });
                return;
            }

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Envoi en cours...';
            submitBtn.disabled = true;

            try {
                const formData = new FormData(this);
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                if (csrfToken) {
                    formData.append('_token', csrfToken);
                }

                console.log('Form data:', Object.fromEntries(formData));

                const response = await fetch('/api/appart/update/' + appart_uuid + '/' + property_uuid, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                });

                const data = await response.json();

                console.log('Response:', data);

                if (!response.ok) {
                    if (data.errors) {
                        const errorMessages = Object.values(data.errors).flat().join('\n');
                        throw new Error(errorMessages);
                    }
                    throw new Error(data.message || 'Erreur lors de l\'envoi');
                }

                // Message de succès
                Swal.fire({
                    icon: 'success',
                    title: 'Succès',
                    text: 'Hébergement modifié avec succès',
                    timer: 5000,
                    showConfirmButton: false,
                    position: 'top-end',
                    progressBar: true,
                    toast: true
                });

                setTimeout(() => {
                    window.location.href = '/partner/property/show/' + property_uuid;
                }, 3000);

            } catch (error) {
                console.error('Erreur:', error);
                Swal.fire({
                    icon: "error",
                    title: "Erreur",
                    text: error.message,
                });
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });
    </script>



    <script>
        const imagesInput = document.getElementById('imagesInput');
        const previewContainer = document.getElementById('previewContainer');
        const filesCount = document.getElementById('filesCount');

        let imageFiles = [];

        imagesInput.addEventListener('change', function() {
            const newFiles = Array.from(imagesInput.files);

            // Fusionner les fichiers actuels et nouveaux sans doublon
            for (const file of newFiles) {
                if (!imageFiles.some(f => f.name === file.name && f.lastModified === file.lastModified)) {
                    imageFiles.push(file);
                }
            }

            renderPreviews();
        });

        function renderPreviews() {
            previewContainer.innerHTML = '';
            filesCount.textContent = imageFiles.length;

            imageFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.classList.add('preview-item');

                    const img = document.createElement('img');
                    img.src = e.target.result;

                    const removeBtn = document.createElement('button');
                    removeBtn.className = 'remove-btn';
                    removeBtn.innerHTML = 'x';
                    removeBtn.onclick = () => {
                        imageFiles.splice(index, 1);
                        renderPreviews();
                    };

                    div.appendChild(img);
                    div.appendChild(removeBtn);
                    previewContainer.appendChild(div);
                };
                reader.readAsDataURL(file);
            });

            // Mettre à jour l'input pour correspondre aux fichiers sélectionnés
            const dataTransfer = new DataTransfer();
            imageFiles.forEach(file => dataTransfer.items.add(file));
            imagesInput.files = dataTransfer.files;
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mapElement = document.getElementById('map-single-property');
            const latitudeInput = document.querySelector('input[name="latitude"]');
            const longitudeInput = document.querySelector('input[name="longitude"]');
            const locationButton = document.querySelector('.btn-location');

            // Valeurs par défaut (centre sur Abidjan si vide)
            const defaultLat = 5.3361;
            const defaultLng = -4.0268;

            const initialLat = parseFloat(latitudeInput.value) || defaultLat;
            const initialLng = parseFloat(longitudeInput.value) || defaultLng;

            // Initialisation de la carte
            const map = L.map(mapElement).setView([initialLat, initialLng], 16);

            // Ajouter le fond de carte
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap',
            }).addTo(map);

            // Ajout du marqueur
            let marker = L.marker([initialLat, initialLng], {
                draggable: true
            }).addTo(map);

            // Mettre à jour les champs quand on déplace le marqueur
            marker.on('dragend', function(e) {
                const pos = marker.getLatLng();
                latitudeInput.value = pos.lat.toFixed(6);
                longitudeInput.value = pos.lng.toFixed(6);
            });

            // Bouton pour localiser automatiquement l'utilisateur
            locationButton.addEventListener('click', function(e) {
                e.preventDefault();
                if (!navigator.geolocation) {
                    alert('La géolocalisation n’est pas supportée par ce navigateur.');
                    return;
                }

                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    latitudeInput.value = lat.toFixed(6);
                    longitudeInput.value = lng.toFixed(6);

                    // Déplacer le marqueur et centrer la carte
                    marker.setLatLng([lat, lng]);
                    map.setView([lat, lng], 16);
                }, function(error) {
                    alert("Impossible d'obtenir votre position.");
                    console.error(error);
                });
            });

            // Geocoder (recherche d’adresse)
            L.Control.geocoder({
                    defaultMarkGeocode: false
                })
                .on('markgeocode', function(e) {
                    const center = e.geocode.center;
                    map.setView(center, 16);
                    marker.setLatLng(center);
                    latitudeInput.value = center.lat.toFixed(6);
                    longitudeInput.value = center.lng.toFixed(6);
                })
                .addTo(map);
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let tarifJour = @json($tarifJour) || null;
            // console.log(tarifJour);
            const addTarifBtn = document.getElementById('addTarifBtn');

            const tarifsContainer = document.getElementById('tarifs-container');
            const tarifBlock = document.querySelector('.tarif-block');

            function isJourAlreadySelected() {
                return Array.from(document.querySelectorAll('.sejour-en')).some(select => select.value === 'Jour');
            }
            function haveDefaultTarifJour() {
                // let defaultTarifJourExists = false;
                // if (tarifJour != null) {
                //     defaultTarifJourExists = true;
                // }
                // return defaultTarifJourExists;
                return tarifJour !== null;
            }

            function updateJourOptionsAvailability() {
                const jourInForm = isJourAlreadySelected();
                const defaultTarifJourExists = haveDefaultTarifJour();
                document.querySelectorAll('.sejour-en').forEach(select => {
                    const jourOption = select.querySelector('option[value="Jour"]');
                    // const jourOption = select.querySelector('option[value="Jour"]');
                    if (!jourOption) return;

                    // autoriser Jour uniquement si :
                    // - pas déjà sélectionné ailleurs
                    // - pas existant en base
                    jourOption.disabled =
                        (jourInForm && select.value !== 'Jour') || defaultTarifJourExists;
                    // if (jourOption) {
                    //     jourOption.disabled = (jourExists && select.value !== 'Jour') || defaultTarifJourExists;
                    //     // if (defaultTarifJourExists) {
                    //     //     jourOption.disabled = true;
                    //     // }
                    // }
                });  
            }

            // Fonction pour gérer le comportement de sélection
            function handleSelectChange(select, tempsInput, tempsLabelSpan) {
                select.addEventListener('change', function() {
                    if (select.value === 'Jour') {
                        tempsInput.value = 1;
                        tempsInput.readOnly = true;
                    } else {
                        tempsInput.value = '';
                        tempsInput.readOnly = false;
                    }
                    tempsLabelSpan.textContent = select.value;

                    // Mettre à jour tous les selects après changement
                    updateJourOptionsAvailability();
                });
            }

            // Appliquer la logique au bloc initial
            const initialSelect = tarifBlock.querySelector('.sejour-en');
            const initialTempsInput = tarifBlock.querySelector('.temps-input');
            const tempsLabelSpan = document.querySelector('.temps-label');
            handleSelectChange(initialSelect, initialTempsInput, tempsLabelSpan);
            updateJourOptionsAvailability();

            // Ajouter un nouveau bloc de tarif
            addTarifBtn.addEventListener('click', function() {
                const newTarifBlock = tarifBlock.cloneNode(true);

                // Réinitialiser les champs
                const select = newTarifBlock.querySelector('.sejour-en');
                const tempsInput = newTarifBlock.querySelector('.temps-input');
                const prixInput = newTarifBlock.querySelector('[name="prix[]"]');
                const tempsLabel = newTarifBlock.querySelector('.temps-label');

                select.value = '';
                tempsInput.value = '';
                tempsInput.readOnly = false;
                prixInput.value = '';
                tempsLabel.textContent = '';

                // Appliquer le comportement dynamique au nouveau bloc
                handleSelectChange(select, tempsInput, tempsLabel);

                // Ajouter bouton de suppression
                const removeBtn = document.createElement('button');
                removeBtn.textContent = 'Supprimer';
                removeBtn.classList.add('tf-btn', 'secondary', 'mt-sm-2', 'mt-lg-4', 'mt-md-4');
                removeBtn.addEventListener('click', function() {
                    newTarifBlock.remove();
                    updateJourOptionsAvailability();
                });

                const removeContainer = newTarifBlock.querySelector('.remove-container');
                removeContainer.innerHTML = '';
                removeContainer.appendChild(removeBtn);

                // Ajouter le nouveau bloc au container
                tarifsContainer.appendChild(newTarifBlock);

                updateJourOptionsAvailability();
            });



            // supprimer une image de l'appartement
            document.querySelectorAll('.removeImage').forEach(function(button) {
                button.addEventListener('click', function() {
                    const previewItem = this.closest('.preview-item'); // corrige ici le sélecteur
                    const imageUuid = previewItem.querySelector('input[name="image_uuid"]').value;

                    Swal.fire({
                        title: 'Êtes-vous sûr ?',
                        text: "Cette image sera définitivement supprimée.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Oui, supprimer !',
                        cancelButtonText: 'Annuler'
                    }).then((result) => {
                        if (result.isConfirmed) {

                            Swal.fire({
                                icon: 'info',
                                title: 'Traitement en cours...',
                                showConfirmButton: false,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            fetch(`/api/delete-appart-image/${imageUuid}`, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]')?.content,
                                        'Accept': 'application/json',
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.status) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Succès',
                                            text: data.message,
                                            timer: 1500,
                                            showConfirmButton: false,
                                            toast: true,
                                            position: 'top-end',
                                            timerProgressBar: true,
                                        });
                                        previewItem.remove();
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Erreur',
                                            text: data.message,
                                            showConfirmButton: true,
                                        });
                                    }
                                })
                                .catch(error => {
                                    console.error(error);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Erreur',
                                        text: 'Une erreur s’est produite lors de la suppression de l’image.',
                                        showConfirmButton: true,
                                    });
                                });
                        }
                    });
                });
            });

            // supprimer un tarif de l'appartement
            document.querySelectorAll('.removeTarif').forEach(function(button) {
                button.addEventListener('click', function() {
                    const previewItem = this.closest('.tarifs-row'); // corrige ici le sélecteur
                    const tarifUuid = previewItem.querySelector('input[name="tarif_uuid"]').value;

                    Swal.fire({
                        title: 'Êtes-vous sûr ?',
                        text: "Cet tarif sera définitivement supprimée.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Oui, supprimer !',
                        cancelButtonText: 'Annuler'
                    }).then((result) => {
                        if (result.isConfirmed) {

                            Swal.fire({
                                icon: 'info',
                                title: 'Traitement en cours...',
                                showConfirmButton: false,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            fetch(`/api/delete-appart-tarif/${tarifUuid}`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]')?.content,
                                    'Accept': 'application/json',
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Succès',
                                        text: data.message,
                                        timer: 1500,
                                        showConfirmButton: false,
                                        toast: true,
                                        position: 'top-end',
                                        timerProgressBar: true,
                                    });
                                    previewItem.remove();
                                    // 🔥 ON DIT AU JS : "le tarif Jour n’existe plus en base"
                                    tarifJour = null;

                                    // 🔥 RÉACTIVATION IMMÉDIATE DE L’OPTION "Jour"
                                    updateJourOptionsAvailability();
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Erreur',
                                        text: data.message,
                                        showConfirmButton: true,
                                    });
                                }
                            })
                            .catch(error => {
                                console.error(error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erreur',
                                    text: 'Une erreur s’est produite lors de la suppression de l’image.',
                                    showConfirmButton: true,
                                });
                            });
                        }
                    });
                });
            });

        });
    </script>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const commoditiesInput = document.getElementById("commoditiesInput");
            const addCommoditiesBtn = document.getElementById("addCommoditiesBtn");
            const selectedContainer = document.getElementById("selectedCommodities");
            const hiddenInput = document.getElementById("hiddenCommoditiesInput");

            // Réinitialiser l'affichage des commodités existantes
            updateHiddenInput();

            addCommoditiesBtn.addEventListener("click", function() {
                const value = commoditiesInput.value.trim();
                if (value && !isAlreadyAdded(value)) {
                    addBadge(value);
                    commoditiesInput.value = "";
                    updateHiddenInput();
                }else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Existe déja',
                        text: "La commodité " + value +  " a déja été ajoutée!",
                        showConfirmButton: true,
                        showConfirmText: 'OK',
                    });
                }
            });

            function isAlreadyAdded(value) {
                return Array.from(selectedContainer.children).some(
                    child => child.querySelector("span").textContent === value
                );
            }

            function addBadge(value) {
                const badge = document.createElement("div");
                badge.className = "commodity-badge text-success font-weight-bold";

                const spanText = document.createElement("span");
                spanText.textContent = value;

                const spanRemove = document.createElement("span");
                spanRemove.className = "remove-commodities";
                spanRemove.textContent = "X";
                spanRemove.style.cursor = "pointer";
                spanRemove.addEventListener("click", () => {
                    badge.remove();
                    updateHiddenInput();
                });

                badge.appendChild(spanText);
                badge.appendChild(spanRemove);
                selectedContainer.appendChild(badge);
            }

            function updateHiddenInput() {
                const values = Array.from(selectedContainer.children).map(
                    child => child.querySelector("span").textContent.trim()
                );
                hiddenInput.value = values.join(',');
            }
            // Permet la suppression des badges existants au chargement
            document.querySelectorAll(".remove-commodities").forEach(btn => {
                btn.addEventListener("click", function() {
                    btn.parentElement.remove();
                    updateHiddenInput();
                });
            });
        });
    </script>
@endsection
