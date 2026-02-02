@extends('layouts.app')

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
</style>
@section('content')
    <div class="main-content-inne pt-5 mt-5 wrap-dashboard-content">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">
                        <i class="fas fa-home me-2 text-danger"></i> Ajout de propriété
                    </h3>
                </div>
            </div>
        </div>
        <form id="editPropertyForm" enctype="multipart/form-data">
            {{-- @csrf --}}
            <input type="hidden" name="partner_uuid" value="{{ Auth::user()->partner_uuid ?? ''}}">
            <input type="hidden" name="user_uuid" value="{{ Auth::user()->uuid ?? ''}}">
             <input type="hidden" name="property_uuid" id="property_uuid" value="{{ $property->uuid ?? '' }}">
            @php
                use Illuminate\Support\Str;
                $cheminExtrait = Str::after($property->image, 'storage/files/');
                $imagePath = base_path(env('STORAGE_FILES') . $cheminExtrait);
            @endphp
            <div class="widget-box-2">
                <h6 class="title">Modifier l'image principale de la propriété</h6>
                <div class="box-uploadfile text-center">
                    <label class="uploadfile">
                        <span class="icon icon-img-2"></span>
                        <div class="btn-upload">
                            <a href="#" class="tf-btn primary">Choisir une image pour modifié l'image</a>
                            <input type="file" class="ip-file" name="image" accept="image/*" @if (!file_exists($imagePath)) required @endif>
                        </div>
                        <p class="file-name fw-5">Ou glisser déposez l'image ici</p>
                    </label>
                </div>
                <div class="pt-3 mt-3 bg-white border border-secondary rounded-3 p-3">
                    <h6>Image principale chargée</h6>
                    @if (file_exists($imagePath))
                        <div class="preview-container d-flex flex-wrap gap-3 mt-3">
                            <div class="preview-item">
                                <img src="{{ asset($property->image) ?? '' }}" alt="">
                            </div>
                        </div>
                    @else
                        <p>Aucune image principale enregistrée.</p>
                    @endif
                </div>
            </div>
            <div class="widget-box-2">
                <h6 class="title">Information sur la propriété</h6>
                <div class="box-info-property">
                    <div class="box grid-2 gap-30">
                        <fieldset class="box box-fieldset">
                            <label for="title">
                                Titre:<span>*</span>
                            </label>
                            <input type="text" class="form-control style-1" value="{{ $property->title ?? '' }}"
                                placeholder="Entrer le titre de la propriété" name="title">
                        </fieldset>
                        <fieldset class="box box-fieldset">
                            <label for="type">
                                Type de propriété:<span>*</span>
                            </label>
                            <select name="type_uuid" class="nice-select form-select list style-1" required id="type">
                                <option value="" disabled selected>-- Choisir le type de propriété --</option>
                                @foreach ($variables as $item)
                                    <option value="{{ $item->uuid }}" @if ($item->uuid == $property->type_uuid) selected @endif>{{ $item->libelle }}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <fieldset class="box box-fieldset">
                        <label for="desc">Description:</label>
                        <textarea class="textarea-tinymce" name="description" placeholder="Entrer la description de la propriété" cols="30" rows="10" id="desc">{{ $property->description ?? '' }}</textarea>
                    </fieldset>

                    <div class="box grid-1 gap-30">
                        <fieldset class="box-fieldset">
                            <label for="address">
                                Adresse complète:<span>*</span>
                            </label>
                            <input type="text" class="form-control style-1"
                                placeholder="Entrer l'adresse complète de la propriété" value="{{ $property->address ?? '' }}" name="address" required>
                        </fieldset>
                        {{-- <fieldset class="box-fieldset">
                            <label for="zip">
                                Code postal:<span></span>
                            </label>
                            <input type="text" class="form-control style-1" placeholder="Entrer le code postal"
                                name="zipCode" value="{{ $property->zipCode ?? '' }}">
                        </fieldset> --}}

                    </div>
                    <div class="box grid-3 gap-30">
                        <fieldset class="box-fieldset">
                            <label for="country">
                                Pays:<span>*</span>
                            </label>
                            <select name="country" class="nice-select form-select list style-1" id="country" required>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->code }}" @if ($country->code == $property->country) selected @endif>{{ $country->label }}</option>
                                @endforeach
                            </select>
                        </fieldset>
                        <fieldset class="box-fieldset">
                            <label for="city">
                                Ville:<span>*</span>
                            </label>

                            <select name="city" class="form-select nice-select list style-1" id="city" required>
                                <option selected value="{{ $property->city ?? '' }}">{{ $property->city ?? '' }}"></option>
                            </select>
                        </fieldset>
                        <fieldset class="box-fieldset">
                            <label for="cityAutre">
                                Precisez la ville:<span>*</span>
                            </label>

                            <input name="cityAutre" class="form-control style-1" id="cityAutre"
                                placeholder="Entrer la ville" value="" disabled>
                        </fieldset>
                    </div>
                    <div class="box box-fieldset">
                        <label for="location">Emplacement:<span>*</span></label>
                        
                        <div class="row">
                            <div class="box-ip col-md-6">
                                <input type="text" name="longitude" value="{{ $property->longitude ?? '' }}" class="form-control style-1" placeholder="Longitude" required>
                            </div>
                            <div class="box-ip col-md-5">
                                <input type="text" name="latitude" value="{{ $property->latitude ?? '' }}" class="form-control style-1" placeholder="Latitude" required>
                            </div>
                            <div class="box-ip col-md-1">
                                <a href="#" class="btn-location"><i class="icon icon-location"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="box box-fieldset">
                        {{-- <div id="map-single" class="map-single" data-map-zoom="16" data-map-scroll="true"></div> --}}
                        <div id="map-single-property" class="map-single" data-map-zoom="16" data-map-scroll="true"></div>
                    </div>

                </div>
            </div>
            <div class="d-flex justify-content-end">
                <button type="submit" class="tf-btn primary"> Ajouter</button>
            </div>
        </form>

    </div>

    <script>
        // document.addEventListener('DOMContentLoaded', function () {
        //     const countrySelect = document.getElementById('country');
        //     const citySelect = document.getElementById('city');

        //     countrySelect.addEventListener('change', function () {
        //         const selectedCountry = this.value;

        //         // Vider les anciennes options
        //         citySelect.innerHTML = '<option value="">Chargement...</option>';

        //         fetch(`/api/get-cities-by-country?country=${selectedCountry}`, {
        //             headers: {
        //                 'Accept': 'application/json'
        //             }
        //         })
        //         .then(response => {
        //             if (!response.ok) {
        //                 throw new Error('Erreur lors de la récupération des villes');
        //             }
        //             return response.json();
        //         })
        //         .then(data => {
        //             citySelect.innerHTML = ''; // Vider le select

        //             if (data.length === 0) {
        //                 citySelect.innerHTML = '<option value="">Aucune ville trouvée</option>';
        //                 return;
        //             }

        //             data.forEach(city => {
        //                 const option = document.createElement('option');
        //                 option.value = city.code;
        //                 option.textContent = city.label; // Assurez-vous que votre modèle `city` a un champ `name`
        //                 citySelect.appendChild(option);
        //             });
        //         })
        //         .catch(error => {
        //             console.error(error);
        //             citySelect.innerHTML = '<option value="">Erreur lors du chargement</option>';
        //         });
        //     });

        //     // Déclencher le chargement des villes au chargement initial si un pays est déjà sélectionné
        //     if (countrySelect.value) {
        //         countrySelect.dispatchEvent(new Event('change'));
        //     }
        // });
        document.addEventListener('DOMContentLoaded', function() {
            const countrySelect = document.getElementById('country');
            const citySelect = document.getElementById('city');
            const cityAutreInput = document.getElementById('cityAutre');

            countrySelect.addEventListener('change', function() {
                const selectedCountry = this.value;

                // Vider les anciennes options
                citySelect.innerHTML = '<option value="">Chargement...</option>';

                fetch(`/api/get-cities-by-country?country=${selectedCountry}`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erreur lors de la récupération des villes');
                        }
                        return response.json();
                    })
                    .then(data => {
                        citySelect.innerHTML = ''; // Vider le select

                        if (data.length === 0) {
                            citySelect.innerHTML = '<option value="">Aucune ville trouvée</option>';
                            return;
                        }

                        data.forEach(city => {
                            const option = document.createElement('option');
                            option.value = city.code;
                            option.textContent = city.label;
                            citySelect.appendChild(option);
                        });

                        // 👉 Ajouter l’option "Autre"
                        const optionAutre = document.createElement('option');
                        optionAutre.value = 'autre';
                        optionAutre.textContent = 'Autre';
                        citySelect.appendChild(optionAutre);
                    })
                    .catch(error => {
                        console.error(error);
                        citySelect.innerHTML = '<option value="">Erreur lors du chargement</option>';
                    });
            });

            // Gestion du choix de la ville
            citySelect.addEventListener('change', function() {
                if (this.value === 'autre') {
                    cityAutreInput.disabled = false;
                    cityAutreInput.required = true;
                    cityAutreInput.focus();
                } else {
                    cityAutreInput.disabled = true;
                    cityAutreInput.required = false;
                    cityAutreInput.value = '';
                }
            });

            // Déclencher le chargement des villes au chargement initial si un pays est déjà sélectionné
            if (countrySelect.value) {
                countrySelect.dispatchEvent(new Event('change'));
            }
        });

        document.getElementById('editPropertyForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const property_uuid = document.getElementById('property_uuid').value;
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
        
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Envoi en cours...';
            submitBtn.disabled = true;
            
            try {
                const formData = new FormData(this);
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                if(csrfToken) {
                    formData.append('_token', csrfToken);
                }

                console.log('Form data:', Object.fromEntries(formData));

                const response = await fetch('/api/property/update/' + property_uuid, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                });

                const data = await response.json();
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
                    text: 'Propriété modifié avec succès',
                    timer: 1500,
                    showConfirmButton: false,
                    position: 'top-end',
                    toast: true
                });

                this.reset();
                // rediriger vers la page des propriétés /partner/my-properties 2 seconde
                setTimeout(() => {
                    window.location.href = '/partner/property/show/' + property_uuid;
                }, 1000);
                
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
        document.addEventListener('DOMContentLoaded', function () {
            const mapElement = document.getElementById('map-single-property');
            const latitudeInput = document.querySelector('input[name="latitude"]');
            const longitudeInput = document.querySelector('input[name="longitude"]');
            const locationButton = document.querySelector('.btn-location');

            // Valeurs par défaut (centre sur Abidjan si vide)
            const defaultLat = latitudeInput.value || 5.3361;
            const defaultLng = longitudeInput.value || -4.0268;

            // const defaultLat = 5.3361;
            // const defaultLng = -4.0268;

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
            marker.on('dragend', function (e) {
                const pos = marker.getLatLng();
                latitudeInput.value = pos.lat.toFixed(6);
                longitudeInput.value = pos.lng.toFixed(6);
            });

            // Bouton pour localiser automatiquement l'utilisateur
            locationButton.addEventListener('click', function (e) {
                e.preventDefault();
                if (!navigator.geolocation) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'La géolocalisation n’est pas supportée par ce navigateur.',
                        timer: 2500,
                        showConfirmButton: false,
                        position: 'top-end',
                        toast: true
                    })
                    return;
                }

                navigator.geolocation.getCurrentPosition(function (position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    latitudeInput.value = lat.toFixed(6);
                    longitudeInput.value = lng.toFixed(6);

                    // Déplacer le marqueur et centrer la carte
                    marker.setLatLng([lat, lng]);
                    map.setView([lat, lng], 16);
                }, function (error) {
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


@endsection
