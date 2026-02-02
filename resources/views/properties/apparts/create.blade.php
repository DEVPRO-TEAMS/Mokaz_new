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
                        <i class="fas fa-home me-2 text-danger"></i> Ajout d'appart dans la propriété #{{ $property->code }}
                    </h3>
                </div>
            </div>
        </div>
        <form id="addAppartForm" enctype="multipart/form-data">
            {{-- @csrf --}}
            <input type="hidden" name="user_uuid" value="{{ Auth::user()->uuid ?? '' }}">
            <input type="hidden" name="partner_uuid" value="{{ Auth::user()->partner_uuid ?? '' }}">
            <input type="hidden" name="property_uuid" id="property_uuid" value="{{ $uuid }}">
            <div class="widget-box-2">
                <h6 class="title">Charger l'images de l'hébergement</h6>
                <div class="box-uploadfile text-center">
                    <label class="uploadfile">
                        <span class="icon icon-img-2"></span>
                        <div class="btn-upload">
                            <a href="#" class="tf-btn primary">Choisir l'image</a>
                            <input type="file" class="ip-file" name="image" accept="image/*" required>
                        </div>
                        <p class="file-name fw-5">Ou glisser déposez l'images ici</p>
                    </label>
                </div>
            </div>
            <div class="widget-box-2">
                <h6 class="title">Information sur l'hébergement</h6>
                <div class="box-info-property">
                    <fieldset class="box box-fieldset">
                        <label for="title">
                            Titre:<span>*</span>
                        </label>
                        <input type="text" class="form-control style-1" value=""
                            placeholder="Entrer le titre de la propriété" name="title" required>
                    </fieldset>
                    <fieldset class="box box-fieldset">
                        <label for="desc">Description:</label>
                        <textarea class="textarea-tinymce" name="description" placeholder="Entrer la description de la propriété"
                            cols="30" rows="10" id="desc"></textarea>
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
                                <select class="form-select list style-1 nice-select sejour-en" name="sejour_en[]" required>
                                    <option value="">Sélectionnez...</option>
                                    <option value="Jour">Jour</option>
                                    <option value="Heure">Heure</option>
                                </select>
                            </fieldset>
                            <fieldset class="box-fieldset col-md-4">
                                <label>
                                    Nombre <span class="temps-label"></span>:<span>*</span>
                                </label>
                                <input type="number" name="temps[]" class="form-control style-1 temps-input"
                                    placeholder="Exemple valeur: 1" required>
                            </fieldset>
                            <fieldset class="box-fieldset col-md-3">
                                <label>
                                    Coût:<span>*</span>
                                </label>
                                <input type="number" name="prix[]" class="form-control style-1"
                                    placeholder="Exemple valeur: 20000" required>
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
            </div>

            <div class="widget-box-2">
                <h6 class="title">Informations complémentaires</h6>
                <div class="row box">
                    <fieldset class="box-fieldset col-md-3">
                        <label for="appartType">
                            Type de l'hébergement:<span>*</span>
                        </label>

                        <select class="form-select nice-select list style-1" id="appartType" name="type_uuid" required>
                            <option value="" disabled selected>-- Choisir le type d'hébergement --</option>
                            @foreach ($typeAppart as $item)
                                <option value="{{ $item->uuid }}">{{ $item->libelle }}</option>
                            @endforeach
                        </select>
                    </fieldset>
                    <fieldset class="box-fieldset col-md-3">
                        <label for="bedrooms">
                            Nombre de chambres:<span>*</span>
                        </label>
                        <input type="number" name="nbr_room" class="form-control style-1" min="0"
                            placeholder="Exemple valeur: 1" required>
                    </fieldset>
                    <fieldset class="box-fieldset col-md-3">
                        <label for="bathrooms">
                            Nombre de salles de bain:<span>*</span>
                        </label>
                        <input type="number" name="nbr_bathroom" class="form-control style-1" min="0"
                            placeholder="Exemple valeur: 1" required>
                    </fieldset>
                    <fieldset class="box-fieldset col-md-3">
                        <label for="neighborhood">
                            Nombre disponible:<span>*</span>
                        </label>
                        <input type="number" name="nbr_available" min="0" class="form-control style-1"
                            placeholder="Exemple valeur: 1">
                    </fieldset>
                </div>
            </div>

            <div class="widget-box-2">
                <h6 class="title">Commodités <span>*</span></h6>
                <div class="box-amenities-proper commodities-container">
                    <div class="box-amenitie commodity-block">
                        <div class="row box">
                            <fieldset class="box-fieldset col-12">
                                <label>
                                    Ajouter une commodité:<span>*</span>
                                </label>
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

                                    <button id="addCommodibtiesBtn" class="tf-btn primary"
                                        type="button">Ajouter</button>
                                </div>
                                <div id="selectedCommodities" class="mt-3">
                                    <!-- Les Commodities saisies apparaîtront ici -->
                                </div>
                                <input type="hidden" id="hiddenCommoditiesInput" name="commodities">
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
                                multiple accept="image/*" required>
                        </div>
                        <p class="file-nam fw-5">
                            Fichiers sélectionnés (<span class="files-count text-danger" id="filesCount">0</span>)
                        </p>
                    </label>
                </div>

                <div id="previewContainer" class="preview-container d-flex flex-wrap gap-3 mt-3"></div>
            </div>

            <div class="widget-box-2">
                <h6 class="title">Videos de l'hébergement</h6>
                <fieldset class="box-fieldset">
                    <label for="video">URL de la vidéo :</label>
                    <input type="url" class="form-control style-1" id="video" name="video_url"
                        placeholder="Youtube de preference">
                </fieldset>
            </div>
            <div class="d-flex justify-content-end">
                {{-- <button type="submit" class="tf-btn primary"> Ajouter</button> --}}
                <button type="submit" name="action" value="continue" class="tf-btn btn-outline-primary me-4"> Ajouter
                    et continuer</button>
                <button type="submit" name="action" value="add" class="tf-btn primary"> Ajouter</button>
            </div>

        </form>

    </div>

    <script>
        // document.getElementById('addAppartForm').addEventListener('submit', async function(e) {
        //     e.preventDefault();
        //     const property_uuid = document.getElementById('property_uuid').value;
        //     const textareaId = 'desc';

        //     // 🔄 Synchroniser le contenu TinyMCE avec le textarea
        //     tinymce.triggerSave();

        //     const content = document.getElementById(textareaId).value.trim();

        //     if (!content) {
        //         Swal.fire({
        //             icon: 'warning',
        //             title: 'Champ requis',
        //             text: 'Le champ de description est obligatoire.',
        //             showConfirmButton: false,
        //             timer: 1500,
        //             progressBar: true

        //         });
        //         return;
        //     }

        //     const submitBtn = this.querySelector('button[type="submit"]');
        //     const originalText = submitBtn.innerHTML;

        //     submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Envoi en cours...';
        //     submitBtn.disabled = true;

        //     try {

        //         const formData = new FormData(this);
        //         const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        //         if (csrfToken) {
        //             formData.append('_token', csrfToken);
        //         }

        //         console.log('Form data:', Object.fromEntries(formData));


        //         const response = await fetch('/api/appart/add', {
        //             method: 'POST',
        //             headers: {
        //                 'Accept': 'application/json',
        //                 'X-CSRF-TOKEN': csrfToken
        //             },
        //             body: formData
        //         });

        //         const data = await response.json();

        //         console.log('Response:', data);

        //         if (!response.ok) {
        //             if (data.errors) {
        //                 const errorMessages = Object.values(data.errors).flat().join('\n');
        //                 throw new Error(errorMessages);
        //             }
        //             throw new Error(data.message || 'Erreur lors de l\'envoi');
        //         }

        //         // ✅ Message de succès
        //         Swal.fire({
        //             icon: 'success',
        //             title: 'Succès',
        //             text: 'Hébergement ajoutée avec succès',
        //             timer: 5000,
        //             showConfirmButton: false,
        //             position: 'top-end',
        //             progressBar: true,
        //             toast: true
        //         });

        //         // const modal = bootstrap.Modal.getInstance(document.getElementById('demandPartnariaModal'));
        //         // modal.hide();
        //         this.reset();
        //         setTimeout(() => {
        //             window.location.href = '/partner/property/show/' + property_uuid;
        //         }, 3000);

        //     } catch (error) {
        //         console.error('Erreur:', error);
        //         Swal.fire({
        //             icon: "error",
        //             title: "Erreur",
        //             text: error.message,
        //         });
        //     } finally {
        //         submitBtn.innerHTML = originalText;
        //         submitBtn.disabled = false;
        //     }
        // });

        document.getElementById('addAppartForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const property_uuid = document.getElementById('property_uuid').value;
            const textareaId = 'desc';

            // 🔄 Synchroniser TinyMCE
            tinymce.triggerSave();

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

            // ✅ Identifier quel bouton a été cliqué
            const submitter = e.submitter; // bouton cliqué
            const action = submitter ? submitter.value : "add"; // par défaut "add"

            const originalText = submitter.innerHTML;
            submitter.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Envoi en cours...';
            submitter.disabled = true;

            try {
                const formData = new FormData(this);
                formData.append("action", action); // on ajoute l’action
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                if (csrfToken) {
                    formData.append('_token', csrfToken);
                }

                console.log('Form data:', Object.fromEntries(formData));

                const response = await fetch('/api/appart/add', {
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

                // ✅ Message succès
                Swal.fire({
                    icon: 'success',
                    title: 'Succès',
                    text: 'Hébergement ajouté avec succès',
                    timer: 3000,
                    showConfirmButton: false,
                    position: 'top-end',
                    toast: true
                });

                if (action === "add") {
                    // 👉 Processus normal : redirection après enregistrement
                    this.reset();
                    setTimeout(() => {
                        window.location.href = '/partner/property/show/' + property_uuid;
                    }, 2000);
                } else if (action === "continue") {
                    // 👉 Ajouter et continuer : pas de reset, pas de reload
                    // tu peux ajouter une logique ici (par ex. vider uniquement certains champs si besoin)
                    console.log("Ajout effectué, on reste sur place (mode continuer).");
                }

            } catch (error) {
                console.error('Erreur:', error);
                Swal.fire({
                    icon: "error",
                    title: "Erreur",
                    text: error.message,
                });
            } finally {
                submitter.innerHTML = originalText;
                submitter.disabled = false;
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

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addTarifBtn = document.getElementById('addTarifBtn');
            const tarifsContainer = document.getElementById('tarifs-container');
            const tarifBlock = document.querySelector('.tarif-block');

            function isJourAlreadySelected() {
                return Array.from(document.querySelectorAll('.sejour-en')).some(select => select.value === 'Jour');
            }

            function updateJourOptionsAvailability() {
                const jourExists = isJourAlreadySelected();
                document.querySelectorAll('.sejour-en').forEach(select => {
                    const jourOption = select.querySelector('option[value="Jour"]');
                    if (jourOption) {
                        jourOption.disabled = jourExists && select.value !== 'Jour';
                    }
                });
            }

            function handleSelectChange(select, tempsInput, tempsLabelSpan) {
                select.addEventListener('change', function() {
                    tempsLabelSpan.textContent = select.value;
                    if (select.value === 'Jour') {
                        tempsInput.value = 1;
                        tempsInput.readOnly = true;
                    } else {
                        tempsInput.value = '';
                        tempsInput.readOnly = false;
                    }

                    // Mettre à jour tous les selects après changement
                    updateJourOptionsAvailability();
                });
            }

            // Initialisation du bloc existant
            const initialSelect = tarifBlock.querySelector('.sejour-en');
            const initialTempsInput = tarifBlock.querySelector('.temps-input');
            const tempsLabelSpan = tarifBlock.querySelector('.temps-label');
            handleSelectChange(initialSelect, initialTempsInput, tempsLabelSpan);
            updateJourOptionsAvailability();

            // Ajouter un nouveau bloc tarif
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

                // Supprimer le bouton existant (au cas où)
                const removeContainer = newTarifBlock.querySelector('.remove-container');
                removeContainer.innerHTML = '';

                // Ajouter bouton de suppression
                const removeBtn = document.createElement('button');
                removeBtn.textContent = 'Supprimer';
                removeBtn.type = 'button';
                removeBtn.classList.add('tf-btn', 'secondary', 'mt-sm-2', 'mt-lg-4', 'mt-md-4');
                removeBtn.addEventListener('click', function() {
                    newTarifBlock.remove();
                    updateJourOptionsAvailability(); // Réactiver "Jour" si besoin
                });
                removeContainer.appendChild(removeBtn);

                // Appliquer le comportement
                handleSelectChange(select, tempsInput, tempsLabel);

                // Ajouter bloc
                tarifsContainer.appendChild(newTarifBlock);

                updateJourOptionsAvailability();
            });
        });
    </script>


    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // Références des éléments
            const commoditiesInput = document.getElementById("commoditiesInput");
            const commoditiesList = document.getElementById("commoditiesList");
            const addCommodibtiesBtn = document.getElementById("addCommodibtiesBtn");
            const selectedCommoditiesContainer = document.getElementById("selectedCommodities");
            const hiddenCommoditiesInput = document.getElementById("hiddenCommoditiesInput");

            // Gestion de l'ajout d'une distraction
            addCommodibtiesBtn.addEventListener("click", function() {
                const commoditiesValue = commoditiesInput.value.trim();
                if (commoditiesValue && !isCommoditiesAlreadyAdded(commoditiesValue)) {
                    addCommodities(commoditiesValue);
                    updateHiddenInput(); // Met à jour l'input caché
                    commoditiesInput.value = ""; // Réinitialise le champ
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Existe déja',
                        text: "La commodité " + commoditiesValue + " a déja été ajoutée!",
                        showConfirmButton: true,
                        showConfirmText: 'OK',
                    });
                }
            });

            // Vérifie si une commodities est déjà ajoutée
            function isCommoditiesAlreadyAdded(value) {
                return Array.from(selectedCommoditiesContainer.children).some(
                    child => child.textContent.trim().startsWith(value)
                );
            }
            // function isAlreadyAdded(value) {
            //     return Array.from(selectedContainer.children).some(
            //         child => child.querySelector("span").textContent === value
            //     );
            // }

            // Ajoute une commodities à la liste sélectionnée
            function addCommodities(value) {
                const badge = document.createElement("div");
                badge.classList.add("commodity-badge");

                const text = document.createElement("span");
                text.textContent = value;

                const removeBtn = document.createElement("span");
                removeBtn.textContent = "x";
                removeBtn.classList.add("remove-commodities");
                removeBtn.title = "Supprimer";
                removeBtn.addEventListener("click", function() {
                    badge.remove();
                    updateHiddenInput();
                });

                badge.appendChild(text);
                badge.appendChild(removeBtn);
                selectedCommoditiesContainer.appendChild(badge);
            }

            // Met à jour l'input caché avec les commodities sélectionnées
            // function updateHiddenInput() {
            //     const selectedCommodities = Array.from(selectedCommoditiesContainer.children).map(
            //         child => child.textContent.replace(" x", "").trim()
            //     );
            //     hiddenCommoditiesInput.value = selectedCommodities.join(
            //         ","); // Stocke les commodities sous forme de chaîne
            // }
            function updateHiddenInput() {
                const selectedCommodities = Array.from(selectedCommoditiesContainer.children).map(
                    child => child.querySelector("span:first-child").textContent.trim()
                );
                hiddenCommoditiesInput.value = selectedCommodities.join(",");
            }
        });
    </script>
@endsection
