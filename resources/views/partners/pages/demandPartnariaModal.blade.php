


     <style>
        .form-control:focus, .form-select:focus {
            border-color: #cd380f;
            box-shadow: 0 0 0 0.2rem rgba(205, 56, 15, 0.25);
        }
        .step-indicator .step {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            font-weight: bold;
            color: #6c757d;
        }
        .step-indicator .step.active {
            background: #cd380f;
            color: white;
        }
        .social-icon {
            color: #cd380f;
            font-size: 1.5rem;
            margin: 0 10px;
        }
    </style>

    <!-- Modal de Demande de Partenariat -->
    <div class="modal fade" id="demandPartnariaModal" tabindex="-1" aria-labelledby="demandPartnariaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-secondary fw-bold text-uppercase" id="demandPartnariaModalLabel">
                        <i class="fas fa-building me-2"></i>Demande de Partenariat Immobilier
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Colonne de gauche - Avantages -->
                        <div class="col-lg-5">
                            <div class="card border-primary mb-4">
                                <div class="card-body">
                                    <h5 class="text-primary mb-4">
                                        <i class="fas fa-rocket me-2"></i>Boostez votre visibilité !
                                    </h5>
                                    <p class="mb-4">Rejoignez notre plateforme multi-services et donnez une nouvelle dimension à vos biens immobiliers.</p>
                                    
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="text-center p-3">
                                                <i class="fas fa-chart-line text-primary mb-3" style="font-size: 2rem;"></i>
                                                <h6>Visibilité Maximale</h6>
                                                <p class="small text-muted">Exposez vos biens à une large audience</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-center p-3">
                                                <i class="fas fa-users text-primary mb-3" style="font-size: 2rem;"></i>
                                                <h6>Clients Qualifiés</h6>
                                                <p class="small text-muted">Accédez à une base de clients ciblés</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-center p-3">
                                                <i class="fas fa-tools text-primary mb-3" style="font-size: 2rem;"></i>
                                                <h6>Outils Performants</h6>
                                                <p class="small text-muted">Profitez de nos outils marketing</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-center p-3">
                                                <i class="fas fa-headset text-primary mb-3" style="font-size: 2rem;"></i>
                                                <h6>Support Dédié</h6>
                                                <p class="small text-muted">Bénéficiez d'un accompagnement</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card border-primary">
                                <div class="card-body">
                                    <h5 class="text-primary mb-3">Contactez-nous</h5>
                                    <p><i class="fas fa-envelope text-primary me-2"></i> <a href="mailto:info@jsbeyci.com">info@jsbeyci.com</a></p>
                                    <p><i class="fas fa-phone text-primary me-2"></i> <a href="tel:+2250787245197">+225 07 87 24 51 97 </a></p>
                                    
                                    <div class="d-flex justify-content-center mt-4">
                                        <a href="#" class="social-icon"><i class="fab fa-facebook"></i></a>
                                        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                                        <a href="#" class="social-icon"><i class="fab fa-linkedin"></i></a>
                                        <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Colonne de droite - Formulaire -->
                        <div class="col-lg-7">
                            <form id="partnershipForm">
                                <div class="d-flex justify-content-center mb-4 step-indicator">
                                    <div class="step active">1</div>
                                    <div class="step">2</div>
                                    <div class="step">3</div>
                                </div>
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="first_name" class="form-label">Prénom <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="last_name" class="form-label">Nom <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="emailPartner" name="email" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Téléphone</label>
                                        <input type="tel" class="form-control" name="phone" id="phone">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="company" class="form-label">Entreprise <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="company" id="company" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="propertyType" class="form-label">Type de biens <span class="text-danger">*</span></label>
                                        <select class="form-select" id="propertyType" name="property_type" required>
                                            <option value="">Sélectionnez...</option>
                                            <option value="residential">Résidentiel</option>
                                            <option value="commercial">Commercial</option>
                                            <option value="industrial">Industriel</option>
                                            <option value="land">Terrains</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label for="city" class="form-label">Zone d'activité <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="city" name="activity_zone" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="website" class="form-label">Site Web</label>
                                        <input type="text" class="form-control" id="website" name="website">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="portfolio" class="form-label">Portefeuille</label>
                                        <select class="form-select" id="portfolio" name="portfolio_size">
                                            <option value="">Nombre de biens</option>
                                            <option value="1-10">1-10 biens</option>
                                            <option value="11-50">11-50 biens</option>
                                            <option value="51+">51+ biens</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label for="message" class="form-label">Message</label>
                                        <textarea class="form-control" id="message" name="message" rows="3" placeholder="Décrivez votre activité..."></textarea>
                                    </div>
                                    <div class="col-12">
                                        <input type="hidden" name="accepts_newsletter" value="0">
                                        <div class="form-check">
                                            <input class="form-check-input" name="accepts_newsletter" type="checkbox" id="terms" value="1" required>
                                            <label class="form-check-label" for="terms">
                                                J'ai et j'accepte les <a href="{{asset('/conditions/cgu.pdf')}}" target="_blank" class="text-primary">conditions génrales d'utilisation</a> <span class="text-danger">*</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-4">
                                        <button type="submit" class="btn btn-outline-danger w-100 py-3">
                                            <i class="fas fa-paper-plane me-2"></i>Envoyer la demande
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('partnershipForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
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

            const response = await fetch('api/partner/demande/store', {
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

            // ✅ Message de succès
            Swal.fire({
                icon: 'success',
                title: 'Succès',
                text: 'Demande envoyée avec succès',
                timer: 5000,
                showConfirmButton: false,
                position: 'top-end',
                toast: true
            });

            const modal = bootstrap.Modal.getInstance(document.getElementById('demandPartnariaModal'));
            modal.hide();
            this.reset();
            
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

    // Animation des étapes
    const inputs = document.querySelectorAll('#partnershipForm input, #partnershipForm select, #partnershipForm textarea');
    const steps = document.querySelectorAll('.step');

    inputs.forEach((input, index) => {
        input.addEventListener('focus', () => {
            const stepIndex = Math.floor(index / (inputs.length / 3));
            steps.forEach((step, i) => {
                step.classList.toggle('active', i <= stepIndex);
            });
        });
    });
</script>
