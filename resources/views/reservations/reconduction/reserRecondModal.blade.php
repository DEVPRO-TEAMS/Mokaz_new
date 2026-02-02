<!-- Modal Multi-Étapes -->
<div class="modal fade" id="reservationReconductionModal" tabindex="-1" aria-labelledby="reservationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-light">
                <h5 class="modal-title text-light" id="reservationModalLabel">
                    <i class="fas fa-calendar-check me-2"></i>Processus de Réservation
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Stepper -->
                <div class="stepper-wrapper mb-4">
                    <div class="stepper-item active" data-step="1">
                        <div class="step-counter bg-danger text-white">1</div>
                        <div class="step-name fw-bold">Informations<br>Personnelles</div>
                    </div>
                    <div class="stepper-item" data-step="2">
                        <div class="step-counter bg-secondary">2</div>
                        <div class="step-name">Facture &<br>Paiement</div>
                    </div>
                    <div class="stepper-item" data-step="3">
                        <div class="step-counter bg-secondary">3</div>
                        <div class="step-name">Confirmation &<br>Reçu</div>
                    </div>
                </div>

                @php
                    $tarifHeure = $appart->tarifications->where('sejour', 'Heure');
                    $tarifJour = $appart->tarifications->where('sejour', 'Jour');
                    $tarifHeureSort = $tarifHeure->sortBy('price')->first();
                    $tarifHeureCount = $tarifHeure->count();
                    $tarifJourCount = $tarifJour->count();
                    $tarifByDay = $tarifJour->first();

                    // Déterminer la sélection par défaut
                    $checkedType = null;
                    if ($tarifHeureCount > 0 && $tarifJourCount > 0) {
                        $checkedType = $tarifHeureCount >= $tarifJourCount ? 'heure' : 'jour';
                    } elseif ($tarifHeureCount > 0) {
                        $checkedType = 'heure';
                    } elseif ($tarifJourCount > 0) {
                        $checkedType = 'jour';
                    }
                @endphp

                <!-- Étape 1: Informations Personnelles -->
                <div class="step-content active" id="step1">
                    <div class="row g-3">
                        <!-- Choix du type de séjour -->
                        @if ($tarifHeureCount > 0 || $tarifJourCount > 0)
                            <div class="col-12">
                                <label class="form-label d-block"><i class="fas fa-clock me-2"></i>Type de séjour
                                    *</label>
                                <div class="d-flex gap-3 flex-wrap">
                                    @if ($tarifJourCount > 0)
                                        <div class="card p-3 flex-fill text-center type-sejour-card"
                                            style="cursor: pointer;">
                                            <input type="radio" name="sejour" id="sejour_jour" class="btn-check"
                                                value="jour" autocomplete="off"
                                                {{ $checkedType === 'jour' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-danger w-100" for="sejour_jour">
                                                <i class="fas fa-calendar-day me-2"></i>Par Jour
                                            </label>
                                        </div>
                                    @endif

                                    @if ($tarifHeureCount > 0)
                                        <div class="card p-3 flex-fill text-center type-sejour-card"
                                            style="cursor: pointer;">
                                            <input type="radio" name="sejour" id="sejour_heure" class="btn-check"
                                                value="heure" autocomplete="off"
                                                {{ $checkedType === 'heure' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-danger w-100" for="sejour_heure">
                                                <i class="fas fa-clock me-2"></i>Par Heure
                                            </label>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Bloc: Dates si "jour" est sélectionné -->
                        <div id="bloc-jour" class="row g-3 {{ $checkedType === 'jour' ? '' : 'd-none' }}">
                            <div class="row col-md-6">
                                <div class="col-md-6">
                                    <label for="start_date_jour" class="form-label">
                                        <i class="fas fa-calendar-alt me-2"></i>Date d'arrivée *
                                    </label>
                                    <input type="date" class="form-control form-control-lg" name="start_date_jour"
                                        id="start_date_jour">
                                </div>
                                <div class="col-md-6">
                                    <label for="start_hour_jour" class="form-label">
                                        <i class="fas fa-calendar-alt me-2"></i>Heure d'arrivée *
                                    </label>
                                    <input type="time" class="form-control form-control-lg" name="start_hour_jour"
                                        id="start_hour_jour">
                                </div>
                            </div>
                            <div class="row col-md-6">
                                <div class="col-md-12">
                                    <label for="end_date_jour" class="form-label">
                                        <i class="fas fa-calendar-alt me-2"></i>Date de départ *
                                    </label>
                                    <input type="date" class="form-control form-control-lg" name="end_date_jour"
                                        id="end_date_jour">
                                </div>

                                {{-- end_hour_jour est à Calculer a partir de start_date_jour et start_hour_jour et end_date_jour. remplir le input apres le calcul --}}
                                <input type="time" class="form-control form-control-lg" name="end_hour_jour"
                                    id="end_hour_jour" hidden>
                            </div>
                        </div>

                        <!-- Bloc: Options si "heure" est sélectionné -->
                        <div id="bloc-heure" class="row g-3 {{ $checkedType === 'heure' ? '' : 'd-none' }}">
                            <div class="col-md-6 d-flex flex-column justify-content-center align-items-start">
                                @foreach ($appart->tarifications->where('etat', '!=', 'inactif')->where('sejour', 'Heure') as $tarif)
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" value="{{ $tarif->price }}"
                                            name="tarif_by_sejour" id="tarifBySejour{{ $tarif->uuid }}"
                                            data-hours="{{ $tarif->nbr_of_sejour }}" required
                                            {{ $loop->first ? 'checked' : '' }}>
                                        <label class="form-check-label" for="tarifBySejour{{ $tarif->uuid }}">
                                            <strong>{{ number_format($tarif->price, 0, ',', ' ') }} FCFA /
                                                {{ $tarif->nbr_of_sejour }}
                                                heure{{ $tarif->nbr_of_sejour > 1 ? 's' : '' }}</strong>
                                        </label>
                                    </div>
                                @endforeach

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" value="custom"
                                        name="tarif_by_sejour" id="tarifCustom">
                                    <label class="form-check-label" for="tarifCustom">
                                        <strong>Autre durée</strong>
                                    </label>
                                </div>
                            </div>
                            <div id="custom-hours-block" class="col-md-6 d-none">
                                <label for="custom_hours" class="form-label">
                                    <i class="fas fa-hourglass-half me-2"></i>Nombre d'heures *
                                </label>
                                <input type="number" min="1" class="form-control form-control-lg"
                                    name="custom_hours" id="custom_hours" placeholder="Ex: 3">
                            </div>

                            <div class="col-12 row w-100">
                                <div class="col-md-6">
                                    <label for="start_date_heure" class="form-label">
                                        <i class="fas fa-calendar-alt me-2"></i>Date d'arrivée *
                                    </label>
                                    <input type="date" class="form-control form-control-lg"
                                        name="start_date_heure" id="start_date_heure">
                                </div>
                                <div class="col-md-6">
                                    <label for="start_time_heure" class="form-label">
                                        <i class="fas fa-clock me-2"></i>Heure d'arrivée *
                                    </label>
                                    <input type="time" class="form-control form-control-lg"
                                        name="start_time_heure" id="start_time_heure">

                                    {{-- end_date_heure et end_time_heure sont à Calculer a partir de start_date_heure et start_time_heure et custom_hours. remplir les inputs apres le calcul --}}
                                    <input type="date" class="form-control form-control-lg" name="end_date_heure"
                                        id="end_date_heure" hidden>
                                    <input type="time" class="form-control form-control-lg" name="end_time_heure"
                                        id="end_time_heure" hidden>
                                </div>
                            </div>
                        </div>

                        <!-- Informations personnelles -->
                        <div class="col-md-6">
                            <label for="nom" class="form-label"><i class="fas fa-user me-2"></i>Nom *</label>
                            <input type="text" class="form-control form-control-lg" name="nom" id="nom"
                                placeholder="Dupont" required>
                        </div>
                        <div class="col-md-6">
                            <label for="prenoms" class="form-label"><i class="fas fa-user me-2"></i>Prénoms
                                *</label>
                            <input type="text" class="form-control form-control-lg" name="prenoms" id="prenoms"
                                placeholder="Jean" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label"><i class="fas fa-envelope me-2"></i>Email
                                *</label>
                            <input type="email" class="form-control form-control-lg" name="email" id="email"
                                placeholder="jean.dupont@example.com" required>
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label"><i class="fas fa-phone me-2"></i>Téléphone
                                *</label>
                            <input type="tel" class="form-control form-control-lg" name="phone" id="phone"
                                placeholder="0123456789" required>
                        </div>

                        <!-- Notes -->
                        <div class="col-12">
                            <label for="notes" class="form-label">
                                <i class="fas fa-comment me-2"></i>Commentaires
                            </label>
                            <textarea class="form-control form-control-lg" name="notes" id="notes" rows="3"
                                placeholder="Demandes spéciales, préférences..."></textarea>
                        </div>
                    </div>

                    <!-- Aperçu du prix -->
                    <div id="price-preview" class="card bg-danger text-white mt-4">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-calculator me-2"></i>Aperçu des prix</h5>
                            <div class="d-flex justify-content-between mb-2">
                                <span id="price-label">Tarif:</span>
                                <span id="unit-price">0 XOF</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span id="duration-label">Durée:</span>
                                <span id="duration-value">0</span>
                            </div>
                            <div class="d-flex justify-content-between border-top pt-2 fw-bold">
                                <span>Total:</span>
                                <span id="total-amount">0 XOF</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Étape 2: Facture et Paiement -->
                <div class="step-content" id="step2">
                    <div class="row g-4">
                        <div class="col-lg-12">
                            <div class="card border-danger">
                                <div class="card-header bg-danger text-white">
                                    <h5 class="mb-0 text-white"><i class="fas fa-file-invoice me-2"></i>Facture</h5>
                                </div>
                                <div class="card-body" id="invoice-details">
                                    <!-- Contenu généré dynamiquement -->
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 d-none">
                            <div class="card border-primary">
                                <div class="card-header bg-danger text-light">
                                    <h5 class="mb-0 text-white"><i class="fas fa-credit-card me-2"></i>Mode de
                                        paiement</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <button class="payment-method btn btn-outline-danger w-100 h-100 py-3"
                                                data-method="CI_PAIEMENTWAVE_TP">
                                                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJQAAACUCAMAAABC4vDmAAAA51BMVEUdyP8BAgL+/v4AAgIAAAD0fyAdyv8AzP+opZ/5fAYez/8ezP8f0//3gSAe0f/7gyH29vZEREQVkLRbW1vj4+Pkdx6CRBENWnMTgqVlZWUMUmkvLy9gMg0SfpsVkroFHykIOEgYo88cwO+Ojo5ycnKCgoK+vr7Ly8uurq54PxAWDAMSeZvEZhoDFx8PZYEHMT8ast5RUVECEBU9IAjVbxwQb4vX19cXFxe4YBiSTBMLTF1tOQ4KRFc0GwekVRZJJgogEQQjJCRXvt3WfT9vqb2Fr7eQo6nQkGNyts/Ikmr/dQBhLABQm7VHc6p4AAAHbElEQVR4nO1cbVPiSBA22IxLQhJAWERZQRQVUEQFX1ZPV/fl9vbu//+em0lI0jOZBLYqHfZDmiotGCwenun3nnFrq5BCCimkkEIKKaSQQpKFWZYphLFNIwmEWVVz1vjEpTFjVfNPwGVZs8P+NgRytTdj1oYhseps0QNJbtqz6kbZsgYLDsMwttGDv7A/MzeHyZn1PUyKAPQOnQ1BYtYeRyTIkZjy2VpYm9lCp6FhKSRr6mwClbmXjMnwuMofk3UEEOxWbPv4A6CRu7azQS+FJ5+rWd5cOVOIaNExxbnazznqWEfbK4gSVB3mu4FsCiuZMuCymiumQZrlRVwd5alVzgTxtPSgodVFS9DOE5TVD4nyo3DCs6tBfqrOZj0Iidm9ax4H7ACcNJtDiBZz3D/hOAMydktcPiyfwol4Noyo2svP/kQkDsg4FTBKvl6BcSaeNEMtg3Z+2YI1ibi4C0CJJ/dND9R9yGM/P6dg7YcGB+cCRm3JlM/bXcQU5AgKGV+9hrTI17AOMsatvMyPbUWuCeDg/HQ3sr76+fl15KlyDMrcn+MIl+ynjBx9AvcIcpDzf+iiH3zKy/zMRizwhQXWtvL6NC9HVZ2CSsjr6KHVmj92xyAtGbCfl6ZXP4NM0dNDy3Vt23bt+eNY1rFnoujHTLk/wNhzlJ2P/+q+CERlX2zXnY9ev4whYKo3IwHlDBqNAVZXNrgKPbbx0iqHiHxx3Vbrr/AN2xSgGA8pXCaotrSiHIHv3oVbVsRtdWE7YAoOs/cJzFr4VjWJthDlCB5XMlO2O39FNkhRaJnTZaiF/UHwla1DkJxnd+66EaTWo5Qp80o5a0zi80PjDlCJHEFyCF8+cljc+viP1uOtXFHA56xDMhtc4o1aNnisqeI7+e6On14eHl5Gt1KQ8dZusm7tSfUBN+8jbytEjqCEGezR5fgDkHH7Bdn+khFhSoxdrlNehX+TsU/APYxlnjJxmFfzaQKy7kFQO/BUPPa9p8yc/QZRIk/I1ifw2lz95gDP3CLjhCQyZcAiW5/A2rp+5rNqfCuoames6Psa5TFiJraCqYzzhJhDWnL1O0RlXroLRV+LklSmMvYJVkr7d32mMvYJwnmuzZQXYLQLGecJzmJNptBoJr42ydgniIC8BlPw1jlt1mq105M3pXAQTO1nnCespVUAHb/LIXobw9gfwFXGaR5zppKm6HgC+FBCcvqmdGhhO+sqi3EHms7VstMSyZmaUUHmVRYz24grHU8KJo7KkOYjFP0EtpXKFRyrmESzUcrSoUFQ0CCu4nZXj2MS7SrATE0I+gkpegX3ZzpQTaWgoWgH+VxpQQ11mKJusf8mmsGRx5Vm+wxo6kE1D9AcF/o0TQ7maLlKIqpUOsZFNEk/YSvkSnEHhlajlloVMmVkXdBEqJy4tsN1EqZSaRdX9gRNjiUqz1/JTGl8VCCnSAcJhyGsqnAFSWru7Z+BlKpN1+Jng2cpL4G3WjKoWgcipiiHIZY8D022PSHHiKkeZTfWkSbH/lgmWalCa4UbylmkaCwilYrlB5JS4TEE6dyBe6vI9t7u0kCVEFMUeUIkuJKAgxTj43KAmJpQgjIbiClt1hLJdRQBaA/jmIeIqRR/LqSDHRXlLMREbSB/4JgsQ8QU1TDEEwe10aCzChRyVJSgzAVi6iQd1DFiiixPECIm2uHXXwkKOX9KRyX5qfV1ijJP4CG5v771nWCmMm58SqDQ/Gqln9rFTD0TJi/4fJl/IiFZ6pipHh1T1hHKPVeFGQMzdUnnE6xD/O31hWggNWn+RgjKnOAPWplPoffe0J3kEEkCSvJS6gavy4G274bOe5pSYz3dJwylt+bHFKSYX7MeFQ4eU2SgTHmslZYPy/08uKTbPmF9uBhNiX5DqSFC6hKO5O+f4qnuZU4JiywpzIiCINEpnMqtI8pyVArIgoC3BEy1jtwMoQzIW1ZbGSonqPqZcvmBNJ9ylKlyUlCuK28jLpHV08tar36Ou8PCo9EebK72FQ5AUyY379U3kVbI3gRJ1qp63C3UlRsZcEl7r0A91CEioKpWHbUPCQvic7HmRGZKFBASV7WOOsuhjDG+sMFNrB97gLK9u+sYT7TtDU9MVauEDXbuvD2s3Z0Y0h02b7VHf1OFeaeZ5ZEx/10fHh8Pr9ELwcmlfC6qiM4nwG139HHUffVvXRjhTchlohUs8xdyupPFN/DpoiUOu9mti1F8PALBslu++Aj9fK4Z7bx/dW3/xKJtu/OufBOSszQPlsW5wW87uWD6Wa7gU532SDrFDN0WPvnpVvJAtfO9Ip+Atd1HxBV0bWW58oNc0dlAwSRQhadyDfjSii1X3qm5Yn9XyqrY83Fwx0Fzarfsfh0QY/oZx8Q/9im449DVrJZ/faelaucfHSh7HvjMhzhRgipaUGZMo/yPXd6bGcc0ysPsvlP6Kvb+S4ep7HZ9h36rI6pcrpDuH/v2q6KVRx/Uv/rVyg9SUO8f9PLN/9cE/yUs/6TNPHcSxPsfDmbS6p/wPxQKKaSQQgoppJBCCtmY/A8K45fbf2GktAAAAABJRU5ErkJggg==" alt="Wave" class="mb-2 rounded"
                                                    style="height:40px;">
                                                <div>Wave</div>
                                            </button>
                                        </div>

                                        <div class="col-6">
                                            <button class="payment-method btn btn-outline-danger w-100 h-100 py-3"
                                                data-method="PAIEMENTMARCHAND_MOOV_CI">
                                                <img src="data:image/png;base64,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" alt="Moov Money" class="mb-2 rounded"
                                                    style="height:40px;">
                                                <div>Moov Money</div>
                                            </button>
                                        </div>

                                        <div class="col-6">
                                            <button class="payment-method btn btn-outline-danger w-100 h-100 py-3"
                                                data-method="PAIEMENTMARCHANDOMPAYCIDIRECT">
                                                <img src="data:image/png;base64,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" alt="Orange Money"
                                                    class="mb-2 rounded" style="height:40px;">
                                                <div>Orange Money</div>
                                            </button>
                                        </div>

                                        <div class="col-6">
                                            <button class="payment-method btn btn-outline-danger w-100 h-100 py-3"
                                                data-method="PAIEMENTMARCHAND_MTN_CI">
                                                <img src="data:image/jpeg;base64,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" alt="MTN Money" class="mb-2 rounded"
                                                    style="height:40px;">
                                                <div>MTN Money</div>
                                            </button>
                                        </div>
                                    </div>


                                    <div id="payment-form" class="mt-4 d-none">
                                        <div class="mb-3">
                                            <label for="card-number" class="form-label">Numéro de paiement
                                               <span class="text-danger">*</span> </label>
                                            <input type="number" class="form-control" id="card-number"
                                                placeholder="ex: 05 04 00 00" name="recipientNumber" min="10" max="10" required>
                                        </div>
                                        
                                        {{-- à affiché si la méthode de paiement est Orange Money data-method="PAIEMENTMARCHANDOMPAYCIDIRECT" --}}
                                        <div class="mb-3 mt-2 d-none">
                                            <label for="expiry-date" class="form-label">Code généré <span class="text-danger">*</span> </label>
                                                <input type="text" class="form-control" id="expiry-date"
                                                    placeholder="ex: 2012" maxlength="4">
                                        </div>

                                        <div class="row g-2" style="display: none;">
                                            <div class="col-md-6">
                                                <label for="expiry-date" class="form-label">Code généré</label>
                                                <input type="text" class="form-control" id="expiry-date"
                                                    placeholder="ex: 2012" maxlength="4">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="cvv-code" class="form-label">CVV</label>
                                                <input type="text" class="form-control" id="cvv-code"
                                                    placeholder="123" maxlength="3">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Étape 3: Confirmation et Reçu -->
                <div class="step-content" id="step3">
                    <div class="text-center mb-4">
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                            <h4>Paiement réussi!</h4>
                            <p class="mb-0">Votre réservation a été confirmée avec succès</p>
                        </div>
                    </div>

                    <div class="card border-danger">
                        <div class="card-header bg-danger-light text-white">
                            <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Reçu de Paiement</h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <p class="text-muted mb-1">Numéro de réservation: <strong
                                        id="reservation-number"></strong></p>
                                <p class="text-muted">Date: <strong id="payment-date"></strong></p>
                            </div>
                            <div id="final-receipt">
                                <!-- Contenu généré dynamiquement -->
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button class="btn btn-success btn-lg" onclick="downloadReceipt()">
                            <i class="fas fa-download me-2"></i>Télécharger le reçu
                        </button>
                    </div>
                </div>

                <!-- Politiques (affiché sur toutes les étapes) -->
                <div class="card mt-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Politiques et Conditions</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="policy-privacy" required>
                            <label class="form-check-label" for="policy-privacy">
                                <strong> <a href="javascript:void(0);" target="_blank" class="text-primary">Politique de confidentialité</a> :</strong> J'accepte que mes données personnelles
                                soient collectées et traitées conformément à la politique de confidentialité
                            </label>
                        </div>
                        {{-- <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="policy-refund" required>
                            <label class="form-check-label" for="policy-refund">
                                <strong>Politique de remboursement:</strong> J'ai lu et j'accepte les conditions de
                                remboursement (remboursement intégral jusqu'à 48h avant l'arrivée)
                            </label>
                        </div> --}}
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="policy-terms" required>
                            <label class="form-check-label" for="policy-terms">
                                <strong> <a href="{{asset('/conditions/cgu.pdf')}}" target="_blank" class="text-primary">Conditions générales d'utilisation </a> :</strong> J'accepte les conditions générales d'utilisation
                                et de séjour
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="prev-btn" onclick="previousStep()"
                    style="display: none;">
                    <i class="fas fa-arrow-left me-2"></i>Précédent
                </button>
                <button type="button" class="btn btn-outline-success" id="next-btn" onclick="nextStep()">
                    Suivant <i class="fas fa-arrow-right ms-2"></i>
                </button>
                <button type="button" class="btn btn-success" id="pay-btn" onclick="processPayment()"
                    style="display: none;">
                    <i class="fas fa-credit-card me-2"></i>Payer maintenant
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="close-btn"
                    style="display: none;">
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Stepper Styles */
    .stepper-wrapper {
        display: flex;
        justify-content: space-between;
        position: relative;
    }

    .stepper-wrapper::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 50px;
        right: 50px;
        height: 2px;
        background: var(--bs-gray-300);
        z-index: 1;
    }

    .stepper-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        z-index: 2;
        flex: 1;
    }

    .stepper-item .step-counter {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-bottom: 8px;
    }

    .stepper-item .step-name {
        font-size: 0.9rem;
        text-align: center;
        color: var(--bs-gray-600);
    }

    .stepper-item.active .step-name {
        color: var(--bs-dark);
        font-weight: 600;
    }

    /* Step Content */
    .step-content {
        display: none;
        animation: fadeIn 0.3s ease-in-out;
    }

    .step-content.active {
        display: block;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Payment Method Active State */
    .payment-method.active {
        background-color: rgba(220, 53, 69, 0.1);
        border-color: var(--bs-danger) !important;
        color: var(--bs-danger);
    }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

{{-- <script src="https://touchpay.gutouch.net/touchpayv2/script/touchpaynr/prod_touchpay-0.0.1.js" type="text/javascript">
</script> --}}


{{-- <script src=https://touchpay.gutouch.com/touchpay/script2/prod_touchpay-0.0.2.js
  type="text/javascript"></script> --}}



<script type="text/javascript">
    // Variables globales
     const reservationOld = @json($reservationOld);
    const DAILY_RATE = @json($tarifByDay->price ?? 0);

    const HOURLY_RATE = @json($tarifHeureSort->price ?? 0);
    const MINIMUM_HOURS = 1;
    let currentStep = 1;
    let reservationData = {};
    let selectedPaymentMethod = null;

    let urlSuccess = "{{ route('reservation.paiement.waiting', ['reservation_uuid' => ':reservation_uuid']) }}";
    let urlFailed = "{{ route('reservation.paiement.failed', ['reservation_uuid' => ':reservation_uuid']) }}";
    let domain_name = "jsbeyci.com";


    // Initialisation
    document.addEventListener('DOMContentLoaded', function() {
        initializeDates();
        setupEventListeners();
        setupSejourTypeToggle();
        updatePricePreview();
    });

    function initializeDates() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('start_date_jour').min = today;
        document.getElementById('end_date_jour').min = today;
        document.getElementById('start_date_heure').min = today;

        const now = new Date();
        const defaultHour = now.getHours() + 1;
        document.getElementById('start_time_heure').value = `${defaultHour.toString().padStart(2, '0')}:00`;
    }

    function setupSejourTypeToggle() {
        const radios = document.querySelectorAll('input[name="sejour"]');
        const tarifRadios = document.querySelectorAll('input[name="tarif_by_sejour"]');
        const blocJour = document.getElementById('bloc-jour');
        const blocHeure = document.getElementById('bloc-heure');
        const customHoursBlock = document.getElementById('custom-hours-block');

        radios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'jour') {
                    blocJour.classList.remove('d-none');
                    blocHeure.classList.add('d-none');
                    // Décocher les tarifs horaires
                    tarifRadios.forEach(radio => {
                        radio.checked = false;
                    });
                    // Supprimer les valeurs
                    document.getElementById('start_time_heure').value = '';
                    document.getElementById('start_date_heure').value = '';
                    document.getElementById('custom_hours').value = '';
                    document.getElementById('end_time_heure').value = '';
                    document.getElementById('end_date_heure').value = '';
                    customHoursBlock.classList.add('d-none');
                } else if (this.value === 'heure') {
                    blocJour.classList.add('d-none');
                    blocHeure.classList.remove('d-none');
                    // Supprimer les valeurs
                    document.getElementById('start_date_jour').value = '';
                    document.getElementById('end_date_jour').value = '';
                    document.getElementById('start_hour_jour').value = '';
                    document.getElementById('end_hour_jour').value = '';
                    // Cocher le premier tarif horaire par défaut
                    if (tarifRadios.length > 0) {
                        tarifRadios[0].checked = true;
                    }
                }
                updatePricePreview();
            });
        });

        tarifRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'custom') {
                    customHoursBlock.classList.remove('d-none');
                } else {
                    customHoursBlock.classList.add('d-none');
                }
                calculateEndDateTimeHeure();
                updatePricePreview();
            });
        });

        // Initialiser l'état selon le type de séjour par défaut
        const defaultType = @json($checkedType ?? '');
        if (defaultType === 'heure' && tarifRadios.length > 0) {
            tarifRadios[0].checked = true;
        }
    }

    function calculateEndHourJour() {
        const startDate = document.getElementById('start_date_jour').value;
        const startHour = document.getElementById('start_hour_jour').value;
        const endDate = document.getElementById('end_date_jour').value;

        if (startDate && startHour && endDate) {
            // Si c'est le même jour, on conserve l'heure de départ
            if (startDate === endDate) {
                document.getElementById('end_hour_jour').value = startHour;
            } else {
                // Si c'est un jour différent, on met l'heure par défaut (12:00)
                // document.getElementById('end_hour_jour').value = '12:00';
                document.getElementById('end_hour_jour').value = startHour;
            }
        }
    }

    function calculateEndDateTimeHeure() {
        const startDate = document.getElementById('start_date_heure').value;
        const startTime = document.getElementById('start_time_heure').value;
        let customHours = 0;

        if (document.getElementById('tarifCustom')?.checked) {
            customHours = parseInt(document.getElementById('custom_hours').value) || 0;
        } else {
            const selected = document.querySelector('input[name="tarif_by_sejour"]:checked');
            if (selected && selected.value !== 'custom') {
                customHours = parseInt(selected.dataset.hours) || 0;
            }
        }

        if (startDate && startTime && customHours > 0) {
            const [year, month, day] = startDate.split('-');
            const [hours, minutes] = startTime.split(':');

            const startDateTime = new Date(year, month - 1, day, hours, minutes);
            const endDateTime = new Date(startDateTime.getTime() + (customHours * 60 * 60 * 1000));

            // Formater la date de fin
            const endDateStr = endDateTime.toISOString().split('T')[0];
            const endTimeStr = endDateTime.toTimeString().substring(0, 5);

            // Mettre à jour les champs cachés
            document.getElementById('end_date_heure').value = endDateStr;
            document.getElementById('end_time_heure').value = endTimeStr;
        }
    }

    function setupEventListeners() {
        // Écouteurs pour les changements de dates/heures
        document.getElementById('start_date_jour').addEventListener('change', function() {
            calculateEndHourJour();
            updatePricePreview();
        });

        document.getElementById('start_hour_jour').addEventListener('change', function() {
            calculateEndHourJour();
            updatePricePreview();
        });

        document.getElementById('end_date_jour').addEventListener('change', function() {
            calculateEndHourJour();
            updatePricePreview();
        });

        document.getElementById('start_date_heure').addEventListener('change', function() {
            calculateEndDateTimeHeure();
            updatePricePreview();
        });

        document.getElementById('start_time_heure').addEventListener('change', function() {
            calculateEndDateTimeHeure();
            updatePricePreview();
        });

        document.getElementById('custom_hours').addEventListener('input', function() {
            calculateEndDateTimeHeure();
            updatePricePreview();
        });

        // Écouteurs pour les méthodes de paiement
        document.querySelectorAll('.payment-method').forEach(method => {
            method.addEventListener('click', function() {
                selectPaymentMethod(this);
            });
        });

        // Réinitialisation du modal quand il est fermé
        document.getElementById('reservationModal').addEventListener('hidden.bs.modal', resetModal);
    }

    function updatePricePreview() {
        const isHourly = document.querySelector('input[name="sejour"]:checked')?.value === 'heure';
        isHourly ? updateHourlyPrice() : updateDailyPrice();
    }

    function updateHourlyPrice() {
        const custom = document.getElementById('tarifCustom')?.checked;
        let hours = 0,
            unitPrice = 0;

        if (custom) {
            hours = parseInt(document.getElementById('custom_hours').value) || 0;
            unitPrice = HOURLY_RATE;
            totalPrice = hours * unitPrice;
        } else {
            const selected = document.querySelector('input[name="tarif_by_sejour"]:checked');
            if (selected && selected.value !== 'custom') {
                hours = parseInt(selected.dataset.hours);
                unitPrice = parseFloat(selected.value);
                totalPrice = unitPrice;
            }
        }

        const total = totalPrice;
        // if (custom) {

        // else {
        //     const total = unitPrice;
        // }
        document.getElementById('unit-price').textContent = unitPrice.toLocaleString('fr-FR') + ' XOF';
        document.getElementById('duration-value').textContent = hours + (hours > 1 ? ' heures' : ' heure');
        document.getElementById('total-amount').textContent = total.toLocaleString('fr-FR') + ' XOF';

        reservationData = {
            ...reservationData,
            isHourly: true,
            hours,
            unitPrice,
            totalPrice: total,
            paymentAmount: total * 0.1,
            startDate: document.getElementById('start_date_heure').value,
            startTime: document.getElementById('start_time_heure').value,
            endDate: document.getElementById('end_date_heure').value,
            endTime: document.getElementById('end_time_heure').value,
            customTarif: custom
        };
    }

    function updateDailyPrice() {
        const start = document.getElementById('start_date_jour').value;
        const end = document.getElementById('end_date_jour').value;

        if (!start || !end) return;

        const startTime = document.getElementById('start_hour_jour').value;
        const endTime = document.getElementById('end_hour_jour').value;

        // Calculer le nombre de jours (arrondi au supérieur)
        const startDateTime = new Date(`${start}T${startTime || '00:00'}`);
        const endDateTime = new Date(`${end}T${endTime || '23:59'}`);
        const diffTime = endDateTime - startDateTime;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        const total = diffDays * DAILY_RATE;
        document.getElementById('unit-price').textContent = DAILY_RATE.toLocaleString('fr-FR') + ' XOF';
        document.getElementById('duration-value').textContent = diffDays + (diffDays > 1 ? ' jours' : ' jour');
        document.getElementById('total-amount').textContent = total.toLocaleString('fr-FR') + ' XOF';

        reservationData = {
            ...reservationData,
            isHourly: false,
            days: diffDays,
            unitPrice: DAILY_RATE,
            totalPrice: total,
            paymentAmount: total * 0.1,
            startDate: start,
            endDate: end,
            startTime: startTime,
            endTime: endTime
        };
    }

    function selectPaymentMethod(method) {
        document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('active'));
        method.classList.add('active');
        selectedPaymentMethod = method.dataset.method;
        document.getElementById('payment-form').classList.remove('d-none');
    }

    function nextStep() {
        if (!validateCurrentStep()) return;

        if (currentStep < 3) {
            currentStep++;
            updateStepDisplay();

            if (currentStep === 2) generateInvoice();
            if (currentStep === 3) generateReceipt();
        }
    }

    function previousStep() {
        if (currentStep > 1) {
            currentStep--;
            updateStepDisplay();
        }
    }

    function validateCurrentStep() {
        const policies = ['policy-privacy', 'policy-terms'];
        const allChecked = policies.every(id => document.getElementById(id).checked);

        if (!allChecked) {
            Swal.fire('⚠️', 'Veuillez accepter toutes les politiques', 'warning');
            return false;
        }

        if (currentStep === 1) {
            const fields = ['nom', 'prenoms', 'email', 'phone'];
            for (const field of fields) {
                if (!document.getElementById(field).value.trim()) {
                    Swal.fire('❌', 'Tous les champs obligatoires doivent être remplis', 'error');
                    return false;
                }
            }

            const isHourly = document.querySelector('input[name="sejour"]:checked')?.value === 'heure';
            if (isHourly) {
                const custom = document.getElementById('tarifCustom')?.checked;
                const hours = custom ?
                    parseInt(document.getElementById('custom_hours').value) :
                    parseInt(document.querySelector('input[name="tarif_by_sejour"]:checked')?.dataset.hours);

                if (!hours || hours < 1) {
                    Swal.fire('❌', 'Veuillez sélectionner une durée valide', 'error');
                    return false;
                }

                if (!document.getElementById('start_date_heure').value || !document.getElementById('start_time_heure')
                    .value) {
                    Swal.fire('❌', 'Veuillez sélectionner une date et une heure', 'error');
                    return false;
                }
            } else {
                const start = document.getElementById('start_date_jour').value;
                const end = document.getElementById('end_date_jour').value;
                const start_hour_jour = document.getElementById('start_hour_jour').value;

                if (!start || !end || new Date(end) < new Date(start)) {
                    Swal.fire('❌', "Le date d'arrivée doit suivre la date de début", 'error');
                    return false;
                }

                if (!start_hour_jour) {
                    Swal.fire('❌', "Veuillez sélectionner une heure d'arrivée", 'error');
                    return false;
                }
            }

            reservationData = {
                ...reservationData,
                nom: document.getElementById('nom').value.trim(),
                prenoms: document.getElementById('prenoms').value.trim(),
                email: document.getElementById('email').value.trim(),
                phone: document.getElementById('phone').value.trim(),
                notes: document.getElementById('notes').value.trim(),
                appart_uuid: @json($appart->uuid ?? null),
                property_uuid: @json($appart->property->uuid ?? null),
                partner_uuid: @json($appart->property->partner->uuid ?? null),
            };
        }

        if (currentStep === 2) {
            // if (!selectedPaymentMethod) {
            //     Swal.fire('❌', 'Veuillez sélectionner un mode de paiement', 'error');
            //     return false;
            // }

            const cardNumber = document.getElementById('card-number').value.trim();
            const cardName = document.getElementById('card-name').value.trim();

            // if (!cardNumber || !cardName) {
            //     Swal.fire('❌', 'Veuillez remplir les informations de paiement', 'error');
            //     return false;
            // }

            reservationData.paymentMethod = selectedPaymentMethod;
            reservationData.cardNumber = cardNumber;
            reservationData.cardName = cardName;
            reservationData.expiry = document.getElementById('expiry-date').value.trim();
            reservationData.cvv = document.getElementById('cvv-code').value.trim();
        }

        return true;
    }

    function updateStepDisplay() {
        document.querySelectorAll('.stepper-item').forEach((item, index) => {
            const counter = item.querySelector('.step-counter');
            item.classList.toggle('active', index === currentStep - 1);
            item.classList.toggle('completed', index < currentStep - 1);
            counter.classList.toggle('bg-secondary', index >= currentStep);
            counter.classList.toggle('bg-success', index < currentStep - 1);
            counter.classList.toggle('bg-danger', index === currentStep - 1);
        });

        document.querySelectorAll('.step-content').forEach((content, index) => {
            content.classList.toggle('active', index === currentStep - 1);
        });

        document.getElementById('prev-btn').style.display = currentStep > 1 ? 'block' : 'none';
        document.getElementById('next-btn').style.display = currentStep < 2 ? 'block' : 'none';
        document.getElementById('pay-btn').style.display = currentStep === 2 ? 'block' : 'none';
        document.getElementById('close-btn').style.display = currentStep === 3 ? 'block' : 'none';
    }
    

    function generateInvoice() {
        const isHourly = reservationData.isHourly;
        const invoiceHTML = `
        <div class="d-flex justify-content-between mb-2"><span>Client:</span><span>${reservationData.prenoms} ${reservationData.nom}</span></div>
        ${isHourly ? `
            <div class="d-flex justify-content-between mb-2"><span>Date et heure:</span><span>${reservationData.startDate} à ${reservationData.startTime}</span></div>
            <div class="d-flex justify-content-between mb-2"><span>Heure de fin:</span><span>${reservationData.endTime}</span></div>
            <div class="d-flex justify-content-between mb-2"><span>Durée:</span><span>${reservationData.hours} heure(s)</span></div>
            <div class="d-flex justify-content-between mb-2"><span>Type tarif:</span><span>${reservationData.customTarif ? 'Personnalisé' : 'Forfait'}</span></div>
        ` : `
            <div class="d-flex justify-content-between mb-2"><span>Période:</span><span>${reservationData.startDate} ${reservationData.startTime ? 'à ' + reservationData.startTime : ''} - ${reservationData.endDate} ${reservationData.endTime ? 'à ' + reservationData.endTime : ''}</span></div>
            <div class="d-flex justify-content-between mb-2"><span>Nuits:</span><span>${reservationData.days}</span></div>
        `}
        <div class="d-flex justify-content-between mb-2"><span>${isHourly ? 'Prix horaire' : 'Prix journalier'}:</span><span>${reservationData.unitPrice.toLocaleString('fr-FR')} XOF</span></div>
        <div class="d-flex justify-content-between border-top pt-2 mb-2"><span>Sous-total:</span><span>${reservationData.totalPrice.toLocaleString('fr-FR')} XOF</span></div>
        <div class="d-flex justify-content-between mb-2"><span>Acompte (10%):</span><span>${reservationData.paymentAmount.toLocaleString('fr-FR')} XOF</span></div>
        <div class="d-flex justify-content-between mb-2"><span>deja payé :</span><span>${reservationOld.payment_amount.toLocaleString('fr-FR')} XOF</span></div>
        <div class="d-flex justify-content-between border-top pt-2 fw-bold"><span>Total à payer:</span><span>${(reservationData.paymentAmount - reservationOld.payment_amount).toLocaleString('fr-FR')  } XOF</span></div>
    `;
        document.getElementById('invoice-details').innerHTML = invoiceHTML;
    }

    // Fonction TouchPay
    function calltouchpay() {
        const order_number = reservationData.reservation.code;
        const agency_code = "JSBEY11380";
        const secure_code = "UYnhBAw9f0A5DshXN8MKA6dg2VZSGs35VrXjETMZSGbJhGlhtw";
        const domain_name = 'jsbeyci.com';
        const url_redirection_success = urlSuccess.replace(':reservation_uuid', reservationData.reservation.uuid);
        const url_redirection_failed = urlFailed.replace(':reservation_uuid', reservationData.reservation.uuid);

        const rest_a_payer = reservationData.paymentAmount - reservationOld.payment_amount;
        // const amount = 200;
        const amount = rest_a_payer;
        const city = "";
        const email = reservationData.email || "";
        const clientFirstname = reservationData.prenoms || "";
        const clientLastname = reservationData.nom || "";
        const clientPhone = reservationData.phone || "";

        sendPaymentInfos(
            order_number,
            agency_code,
            secure_code,
            domain_name,
            url_redirection_success,
            url_redirection_failed,
            amount,
            city,
            email,
            clientFirstname,
            clientLastname,
            clientPhone
        );
    }

    async function processPayment() {
        Swal.fire({
            title: 'Traitement du paiement...',
            text: 'Veuillez patienter',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        try {
            const payload = {
                ...reservationData,
                sejour: reservationData.isHourly ? 'Heure' : 'Jour',
                nbr_of_sejour: reservationData.isHourly ? reservationData.hours : reservationData.days,
                start_time: reservationData.isHourly ?
                    `${reservationData.startDate} ${reservationData.startTime}:00` :
                    `${reservationData.startDate} ${reservationData.startTime || '00:00:00'}`,
                end_time: reservationData.isHourly ?
                    `${reservationData.endDate} ${reservationData.endTime}:00` :
                    `${reservationData.endDate} ${reservationData.endTime || '23:59:59'}`,
                payment_method: reservationData.paymentMethod,
                deja_paye: reservationOld.payment_amount,
                reservation_old_uuid: reservationOld.uuid
            };

            const res = await fetch('/api/reconduction/store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json();
            if (!data.success) throw new Error(data.message || 'Erreur inconnue');

            reservationData.reservation = data.reservation;
            reservationData.pdfUrl = data.pdf_url;

            Swal.close();
            calltouchpay();
            // } else {
            //     nextStep();
            // }

        } catch (err) {
            Swal.fire('❌ Erreur', err.message || 'Erreur serveur', 'error');
        }
    }

    function generateReceipt() {
        if (!reservationData.reservation) return;

        const r = reservationData.reservation;
        document.getElementById('reservation-number').textContent = r.code;
        document.getElementById('payment-date').textContent = new Date().toLocaleDateString('fr-FR');

        const receiptHTML = `
        <div class="d-flex justify-content-between mb-2"><span>Référence:</span><span>${r.code}</span></div>
        <div class="d-flex justify-content-between mb-2"><span>Date paiement:</span><span>${new Date().toLocaleString('fr-FR')}</span></div>
        <div class="d-flex justify-content-between mb-2"><span>Moyen de paiement:</span><span>${reservationData.paymentMethod}</span></div>
        <div class="d-flex justify-content-between mb-2"><span>Montant payé:</span><span>${r.payment_amount.toLocaleString('fr-FR')} XOF</span></div>
        <div class="d-flex justify-content-between border-top pt-2 mb-2"><span>Statut:</span><span class="badge bg-success">Payé</span></div>
        <div class="mt-3 p-3 bg-light rounded">
            <h6>Détails</h6>
            <p class="mb-1"><strong>${r.prenoms} ${r.nom}</strong></p>
            <p class="mb-1">${r.email}</p>
            <p class="mb-1">${r.phone}</p>
            <p class="mb-0 mt-2">
                ${r.sejour === 'Heure' ? `
                    Type: Réservation horaire<br>
                    Date: ${r.start_time.split(' ')[0]}<br>
                    Heure de début: ${r.start_time.split(' ')[1]}<br>
                    Heure de fin: ${r.end_time.split(' ')[1]}<br>
                    Durée: ${r.nbr_of_sejour} heure(s)
                ` : `
                    Type: Réservation journalière<br>
                    Arrivée: ${r.start_time}<br>
                    Départ: ${r.end_time}<br>
                    Nuits: ${r.nbr_of_sejour}
                `}
            </p>
        </div>
    `;
        document.getElementById('final-receipt').innerHTML = receiptHTML;
    }

    function downloadReceipt() {
        if (reservationData.reservation?.uuid) {
            window.location.href = '/api/reservation/download-receipt/' + reservationData.reservation.uuid;
        }
    }

    function resetModal() {
        currentStep = 1;
        reservationData = {};
        selectedPaymentMethod = null;
        document.querySelector('form')?.reset();
        document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('active'));
        document.getElementById('payment-form').classList.add('d-none');

        // Réinitialiser les champs cachés
        document.getElementById('end_hour_jour').value = '';
        document.getElementById('end_date_heure').value = '';
        document.getElementById('end_time_heure').value = '';

        updateStepDisplay();
        initializeDates();

        // Réinitialiser l'affichage des blocs selon le type par défaut
        const defaultType = @json($checkedType ?? '');
        document.getElementById('bloc-jour').classList.toggle('d-none', defaultType !== 'jour');
        document.getElementById('bloc-heure').classList.toggle('d-none', defaultType !== 'heure');
    }
</script>
