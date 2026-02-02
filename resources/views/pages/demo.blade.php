<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système de Réservation Multi-Étapes</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.32/sweetalert2.all.min.js"></script>

</head>

<body>
    <div class="container-fluid">
        <div class="hero-section">
            <h1><i class="fas fa-hotel"></i> Hôtel Luxe</h1>
            <p>Découvrez le confort et l'élégance dans notre établissement de prestige</p>
            <button class="btn btn-reservation" data-bs-toggle="modal" data-bs-target="#reservationModal">
                <i class="fas fa-calendar-plus"></i> Faire une réservation
            </button>
        </div>
    </div>

    <!-- Modal Multi-Étapes -->
    <div class="modal fade" id="reservationModal" tabindex="-1" aria-labelledby="reservationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reservationModalLabel">
                        <i class="fas fa-calendar-check"></i> Processus de Réservation
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Stepper -->
                    <div class="stepper-wrapper">
                        <div class="stepper-item active" data-step="1">
                            <div class="step-counter">1</div>
                            <div class="step-name">Informations<br>Personnelles</div>
                        </div>
                        <div class="stepper-item" data-step="2">
                            <div class="step-counter">2</div>
                            <div class="step-name">Facture &<br>Paiement</div>
                        </div>
                        <div class="stepper-item" data-step="3">
                            <div class="step-counter">3</div>
                            <div class="step-name">Confirmation &<br>Reçu</div>
                        </div>
                    </div>

                    <!-- Étape 1: Informations Personnelles -->
                    <div class="step-content active" id="step1">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nom" class="form-label">
                                        <i class="fas fa-user"></i> Nom *
                                    </label>
                                    <input type="text" class="form-control" id="nom" placeholder="Dupont"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="prenoms" class="form-label">
                                        <i class="fas fa-user"></i> Prénoms *
                                    </label>
                                    <input type="text" class="form-control" id="prenoms" placeholder="Jean"
                                        required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope"></i> Email *
                                    </label>
                                    <input type="email" class="form-control" id="email"
                                        placeholder="jean.dupont@example.com" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">
                                        <i class="fas fa-phone"></i> Téléphone *
                                    </label>
                                    <input type="tel" class="form-control" id="phone" placeholder="0123456789"
                                        required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_time" class="form-label">
                                        <i class="fas fa-calendar-alt"></i> Date d'arrivée *
                                    </label>
                                    <input type="date" class="form-control" id="start_time" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_time" class="form-label">
                                        <i class="fas fa-calendar-alt"></i> Date de départ *
                                    </label>
                                    <input type="date" class="form-control" id="end_time" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">
                                <i class="fas fa-comment"></i> Commentaires
                            </label>
                            <textarea class="form-control" id="notes" rows="3" placeholder="Demandes spéciales, préférences..."></textarea>
                        </div>

                        <div id="pricePreview" class="price-card" style="display: none;">
                            <h5><i class="fas fa-calculator"></i> Aperçu des prix</h5>
                            <div class="price-item">
                                <span>Prix par jour:</span>
                                <span>150,000 XOF</span>
                            </div>
                            <div class="price-item">
                                <span>Nombre de jours:</span>
                                <span id="daysCount">0</span>
                            </div>
                            <div class="price-item total">
                                <span>Total:</span>
                                <span id="totalAmount">0 XOF</span>
                            </div>
                        </div>
                    </div>

                    <!-- Étape 2: Facture et Paiement -->
                    <div class="step-content" id="step2">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="receipt-card">
                                    <div class="receipt-header">
                                        <h3><i class="fas fa-file-invoice"></i> Facture</h3>
                                    </div>
                                    <div id="invoiceDetails">
                                        <!-- Contenu généré dynamiquement -->
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <h5><i class="fas fa-credit-card"></i> Mode de paiement</h5>
                                <div class="payment-methods">
                                    <div class="payment-method" data-method="visa">
                                        <i class="fab fa-cc-visa"></i>
                                        <div>Visa</div>
                                    </div>
                                    <div class="payment-method" data-method="mastercard">
                                        <i class="fab fa-cc-mastercard"></i>
                                        <div>Mastercard</div>
                                    </div>
                                    <div class="payment-method" data-method="orange">
                                        <i class="fas fa-mobile-alt"></i>
                                        <div>Orange Money</div>
                                    </div>
                                    <div class="payment-method" data-method="mtn">
                                        <i class="fas fa-mobile-alt"></i>
                                        <div>MTN Money</div>
                                    </div>
                                </div>

                                <div id="paymentForm" style="display: none;">
                                    <div class="mb-3">
                                        <label for="cardNumber" class="form-label">Numéro de carte / Téléphone
                                            *</label>
                                        <input type="text" class="form-control" id="cardNumber"
                                            placeholder="1234 5678 9012 3456" required>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="expiry" class="form-label">Date d'expiration</label>
                                                <input type="text" class="form-control" id="expiry"
                                                    placeholder="MM/YY">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="cvv" class="form-label">CVV</label>
                                                <input type="text" class="form-control" id="cvv"
                                                    placeholder="123" maxlength="3">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="cardName" class="form-label">Nom sur la carte *</label>
                                        <input type="text" class="form-control" id="cardName"
                                            placeholder="JEAN DUPONT" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Étape 3: Confirmation et Reçu -->
                    <div class="step-content" id="step3">
                        <div class="text-center mb-4">
                            <div class="alert alert-success" role="alert">
                                <i class="fas fa-check-circle fa-2x mb-2"></i>
                                <h4>Paiement réussi!</h4>
                                <p class="mb-0">Votre réservation a été confirmée avec succès</p>
                            </div>
                        </div>

                        <div class="receipt-card">
                            <div class="receipt-header">
                                <h3><i class="fas fa-receipt"></i> Reçu de Paiement</h3>
                                <p class="text-muted">Numéro de réservation: <strong id="reservationNumber"></strong>
                                </p>
                                <p class="text-muted">Date: <strong id="paymentDate"></strong></p>
                            </div>
                            <div id="finalReceipt">
                                <!-- Contenu généré dynamiquement -->
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button class="btn btn-success btn-lg" onclick="downloadReceipt()">
                                <i class="fas fa-download"></i> Télécharger le reçu
                            </button>
                        </div>
                    </div>

                    <!-- Politiques (affiché sur toutes les étapes) -->
                    <div class="policy-section">
                        <h6><i class="fas fa-shield-alt"></i> Politiques et Conditions</h6>
                        <div class="policy-item">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="policyPrivacy" required>
                                <label class="form-check-label" for="policyPrivacy">
                                    <strong>Politique de confidentialité:</strong> J'accepte que mes données
                                    personnelles soient collectées et traitées conformément à la politique de
                                    confidentialité
                                </label>
                            </div>
                        </div>
                        <div class="policy-item">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="policyRefund" required>
                                <label class="form-check-label" for="policyRefund">
                                    <strong>Politique de remboursement:</strong> J'ai lu et j'accepte les conditions de
                                    remboursement (remboursement intégral jusqu'à 48h avant l'arrivée)
                                </label>
                            </div>
                        </div>
                        <div class="policy-item">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="policyTerms" required>
                                <label class="form-check-label" for="policyTerms">
                                    <strong>Conditions générales:</strong> J'accepte les conditions générales
                                    d'utilisation et de séjour
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-step" id="prevBtn"
                        onclick="previousStep()" style="display: none;">
                        <i class="fas fa-arrow-left"></i> Précédent
                    </button>
                    <button type="button" class="btn btn-primary btn-step" id="nextBtn" onclick="nextStep()">
                        Suivant <i class="fas fa-arrow-right"></i>
                    </button>
                    <button type="button" class="btn btn-success btn-step" id="payBtn"
                        onclick="processPayment()" style="display: none;">
                        <i class="fas fa-credit-card"></i> Payer maintenant
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="closeBtn"
                        style="display: none;">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Variables globales
        const DAILY_RATE = 150000;
        let currentStep = 1;
        let reservationData = {};
        let selectedPaymentMethod = null;

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            initializeDates();
            setupEventListeners();
        });

        function initializeDates() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('start_time').min = today;
            document.getElementById('end_time').min = today;
        }

        function setupEventListeners() {
            // Écouteurs pour les dates
            document.getElementById('start_time').addEventListener('change', function() {
                const endDateInput = document.getElementById('end_time');
                if (this.value) {
                    endDateInput.min = this.value;
                    calculatePrice();
                }
            });

            document.getElementById('end_time').addEventListener('change', function() {
                const startDate = new Date(document.getElementById('start_time').value);
                const endDate = new Date(this.value);

                if (endDate <= startDate) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur de date',
                        text: 'La date de départ doit être postérieure à la date d\'arrivée'
                    });
                    this.value = '';
                    return;
                }
                calculatePrice();
            });

            // Écouteurs pour les modes de paiement
            document.querySelectorAll('.payment-method').forEach(method => {
                method.addEventListener('click', function() {
                    selectPaymentMethod(this);
                });
            });

            // Reset modal au close
            document.getElementById('reservationModal').addEventListener('hidden.bs.modal', function() {
                resetModal();
            });
        }

        function calculatePrice() {
            const startDate = document.getElementById('start_time').value;
            const endDate = document.getElementById('end_time').value;

            if (!startDate || !endDate) return;

            const start = new Date(startDate);
            const end = new Date(endDate);
            const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24));

            if (days > 0) {
                const total = days * DAILY_RATE;
                const paymentAmount = total * 0.1; // 10% d'acompte

                document.getElementById('daysCount').textContent = days;
                document.getElementById('totalAmount').textContent = total.toLocaleString('fr-FR') + ' XOF';
                document.getElementById('pricePreview').style.display = 'block';

                // Stocker les données
                reservationData.days = days;
                reservationData.totalPrice = total;
                reservationData.paymentAmount = paymentAmount;
            }
        }

        function nextStep() {
            if (validateCurrentStep()) {
                if (currentStep < 3) {
                    currentStep++;
                    updateStepDisplay();

                    if (currentStep === 2) {
                        generateInvoice();
                    }
                }
            }
        }

        function previousStep() {
            if (currentStep > 1) {
                currentStep--;
                updateStepDisplay();
            }
        }

        function validateCurrentStep() {
            const policiesChecked = document.querySelectorAll('.policy-section input[type="checkbox"]:checked')
                .length === 3;

            if (!policiesChecked) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Politiques requises',
                    text: 'Vous devez accepter toutes les politiques pour continuer'
                });
                return false;
            }

            if (currentStep === 1) {
                // Validation étape 1
                const requiredFields = ['nom', 'prenoms', 'email', 'phone', 'start_time', 'end_time'];
                const emptyFields = requiredFields.filter(field => !document.getElementById(field).value);

                if (emptyFields.length > 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Champs requis',
                        text: 'Veuillez remplir tous les champs obligatoires'
                    });
                    return false;
                }

                if (!reservationData.days || reservationData.days <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Dates invalides',
                        text: 'Veuillez sélectionner des dates valides'
                    });
                    return false;
                }

                // Stocker les données
                reservationData.nom = document.getElementById('nom').value;
                reservationData.prenoms = document.getElementById('prenoms').value;
                reservationData.email = document.getElementById('email').value;
                reservationData.phone = document.getElementById('phone').value;
                reservationData.startDate = document.getElementById('start_time').value;
                reservationData.endDate = document.getElementById('end_time').value;
                reservationData.notes = document.getElementById('notes').value;

                return true;
            }

            if (currentStep === 2) {
                // Validation étape 2
                if (!selectedPaymentMethod) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Mode de paiement requis',
                        text: 'Veuillez sélectionner un mode de paiement'
                    });
                    return false;
                }

                const cardNumber = document.getElementById('cardNumber').value;
                const cardName = document.getElementById('cardName').value;

                if (!cardNumber || !cardName) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Informations de paiement',
                        text: 'Veuillez remplir les informations de paiement'
                    });
                    return false;
                }

                return true
            }

            return true;
        }

        function updateStepDisplay() {
            const steps = document.querySelectorAll('.step');
            steps.forEach((step, index) => {
                step.classList.toggle('active', index === currentStep - 1);
            });

            if (currentStep === 1) {
                document.getElementById('step1').scrollIntoView({ behavior: 'smooth' });
            } else if (currentStep === 2) {
                document.getElementById('step2').scrollIntoView({ behavior: 'smooth' });
            }

        }

        function selectPaymentMethod(method) {
            const paymentMethods = document.querySelectorAll('.payment-method');
            paymentMethods.forEach(method => method.classList.remove('selected'));
            method.classList.add('selected');
            selectedPaymentMethod = method;
        }

        setupEventListeners();
    </script>
    @endpush
