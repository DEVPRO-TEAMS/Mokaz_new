{{-- <script>
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
            document.getElementById('pricePreview').classList.remove('d-none');

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
                } else if (currentStep === 3) {
                    generateReceipt();
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
        const policiesChecked = document.querySelectorAll('#policyPrivacy:checked, #policyRefund:checked, #policyTerms:checked').length === 3;

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
            const emptyFields = requiredFields.filter(field => !document.getElementById(field).value.trim());

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
            reservationData = {
                ...reservationData,
                nom: document.getElementById('nom').value.trim(),
                prenoms: document.getElementById('prenoms').value.trim(),
                email: document.getElementById('email').value.trim(),
                phone: document.getElementById('phone').value.trim(),
                startDate: document.getElementById('start_time').value,
                endDate: document.getElementById('end_time').value,
                notes: document.getElementById('notes').value.trim()
            };

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

            const cardNumber = document.getElementById('cardNumber').value.trim();
            const cardName = document.getElementById('cardName').value.trim();

            if (!cardNumber || !cardName) {
                Swal.fire({
                    icon: 'error',
                    title: 'Informations de paiement',
                    text: 'Veuillez remplir les informations de paiement'
                });
                return false;
            }

            reservationData.paymentMethod = selectedPaymentMethod.dataset.method;
            reservationData.cardNumber = cardNumber;
            reservationData.cardName = cardName;
            reservationData.expiry = document.getElementById('expiry').value.trim();
            reservationData.cvv = document.getElementById('cvv').value.trim();

            return true;
        }

        return true;
    }

    function updateStepDisplay() {
        // Mettre à jour le stepper
        document.querySelectorAll('.stepper-item').forEach((item, index) => {
            const stepCounter = item.querySelector('.step-counter');
            if (index < currentStep - 1) {
                item.classList.add('completed');
                item.classList.remove('active');
                stepCounter.classList.remove('bg-secondary');
                stepCounter.classList.add('bg-success');
            } else if (index === currentStep - 1) {
                item.classList.add('active');
                item.classList.remove('completed');
                stepCounter.classList.remove('bg-secondary');
                stepCounter.classList.add('bg-primary');
            } else {
                item.classList.remove('active', 'completed');
                stepCounter.classList.remove('bg-primary', 'bg-success');
                stepCounter.classList.add('bg-secondary');
            }
        });

        // Afficher/masquer le contenu des étapes
        document.querySelectorAll('.step-content').forEach((content, index) => {
            content.classList.toggle('active', index === currentStep - 1);
        });

        // Gérer les boutons de navigation
        document.getElementById('prevBtn').style.display = currentStep > 1 ? 'block' : 'none';
        document.getElementById('nextBtn').style.display = currentStep < 3 ? 'block' : 'none';
        document.getElementById('payBtn').style.display = currentStep === 2 ? 'block' : 'none';
        document.getElementById('closeBtn').style.display = currentStep === 3 ? 'block' : 'none';

        // Faire défiler vers le haut de l'étape
        document.getElementById(`step${currentStep}`).scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function selectPaymentMethod(method) {
        document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('active'));
        method.classList.add('active');
        selectedPaymentMethod = method;
        
        // Afficher le formulaire de paiement
        document.getElementById('paymentForm').classList.remove('d-none');
    }

    function generateInvoice() {
        const invoiceHTML = `
            <div class="d-flex justify-content-between mb-2">
                <span>Client:</span>
                <span>${reservationData.prenoms} ${reservationData.nom}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span>Période:</span>
                <span>${formatDate(reservationData.startDate)} - ${formatDate(reservationData.endDate)}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span>Durée:</span>
                <span>${reservationData.days} jours</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span>Prix journalier:</span>
                <span>${DAILY_RATE.toLocaleString('fr-FR')} XOF</span>
            </div>
            <div class="d-flex justify-content-between border-top pt-2 mb-2">
                <span>Sous-total:</span>
                <span>${reservationData.totalPrice.toLocaleString('fr-FR')} XOF</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span>Acompte (10%):</span>
                <span>${reservationData.paymentAmount.toLocaleString('fr-FR')} XOF</span>
            </div>
            <div class="d-flex justify-content-between border-top pt-2 fw-bold">
                <span>Total à payer:</span>
                <span>${reservationData.paymentAmount.toLocaleString('fr-FR')} XOF</span>
            </div>
        `;
        document.getElementById('invoiceDetails').innerHTML = invoiceHTML;
    }

    function processPayment() {
        // Simulation de traitement de paiement
        Swal.fire({
            title: 'Traitement du paiement',
            html: 'Veuillez patienter...',
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();
                // Simuler un délai de traitement
                setTimeout(() => {
                    Swal.close();
                    nextStep();
                }, 2000);
            }
        });
    }

    function generateReceipt() {
        // Générer un numéro de réservation aléatoire
        const reservationNumber = 'RES-' + Math.floor(100000 + Math.random() * 900000);
        reservationData.reservationNumber = reservationNumber;
        reservationData.paymentDate = new Date().toLocaleDateString('fr-FR');
        
        document.getElementById('reservationNumber').textContent = reservationNumber;
        document.getElementById('paymentDate').textContent = reservationData.paymentDate;

        const receiptHTML = `
            <div class="d-flex justify-content-between mb-2">
                <span>Référence:</span>
                <span>${reservationNumber}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span>Date paiement:</span>
                <span>${reservationData.paymentDate}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span>Moyen de paiement:</span>
                <span>${getPaymentMethodName(reservationData.paymentMethod)}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span>Montant payé:</span>
                <span>${reservationData.paymentAmount.toLocaleString('fr-FR')} XOF</span>
            </div>
            <div class="d-flex justify-content-between border-top pt-2 mb-2">
                <span>Statut:</span>
                <span class="badge bg-success">Payé</span>
            </div>
            <div class="mt-4 p-3 bg-light rounded">
                <h6 class="mb-2">Détails de la réservation</h6>
                <p class="mb-1">${reservationData.prenoms} ${reservationData.nom}</p>
                <p class="mb-1">${reservationData.email}</p>
                <p class="mb-1">${reservationData.phone}</p>
                <p class="mb-0 mt-2">Période: ${formatDate(reservationData.startDate)} - ${formatDate(reservationData.endDate)}</p>
            </div>
        `;
        document.getElementById('finalReceipt').innerHTML = receiptHTML;
    }


    
    function downloadReceipt() {
        const doc = new jsPDF();

        const reservationData = {
            nom: "Jean Dupont",
            date: "13/07/2025",
            montant: "25.000 FCFA",
            référence: "RES12345"
        };

        doc.setFontSize(16);
        doc.text("Reçu de Réservation", 20, 20);

        doc.setFontSize(12);
        doc.text(`Nom : ${reservationData.nom}`, 20, 40);
        doc.text(`Date : ${reservationData.date}`, 20, 50);
        doc.text(`Montant : ${reservationData.montant}`, 20, 60);
        doc.text(`Référence : ${reservationData.référence}`, 20, 70);

        doc.save("recu_reservation.pdf");

        Swal.fire({
            icon: 'success',
            title: 'Téléchargement du reçu',
            text: 'Votre reçu a été généré avec succès',
            timer: 2000,
            showConfirmButton: false
        });
    }


    function resetModal() {
        // Réinitialiser le formulaire
        document.getElementById('reservationForm').reset();
        document.getElementById('pricePreview').classList.add('d-none');
        document.getElementById('paymentForm').classList.add('d-none');
        
        // Réinitialiser les étapes
        currentStep = 1;
        updateStepDisplay();
        
        // Réinitialiser les données
        reservationData = {};
        selectedPaymentMethod = null;
        
        // Désélectionner les méthodes de paiement
        document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('active'));
    }

    // Fonctions utilitaires
    function formatDate(dateString) {
        const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
        return new Date(dateString).toLocaleDateString('fr-FR', options);
    }

    function getPaymentMethodName(method) {
        const methods = {
            'visa': 'Visa',
            'mastercard': 'Mastercard',
            'orange': 'Orange Money',
            'mtn': 'MTN Money'
        };
        return methods[method] || method;
    }
</script> --}}