<div class="modal fade" id="showReservationModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="flat-account bg-surface">
                <h3 class="title text-center">Suivre ma Réservation</h3>
                <span class="close-modal icon-close2" data-bs-dismiss="modal"></span>
                <form id="showReservationForm">
                    <fieldset class="box-fieldset">
                        <label for="code">Code de réservation<span>*</span>:</label>
                        <input id="code" type="text" class="form-control style-1" name="code"
                            value="{{ old('code') }}" required autofocus>
                    </fieldset>
                    <button type="submit" class="tf-btn primary w-100">Suivre</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('showReservationForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Recherche en cours...';
        submitBtn.disabled = true;

        try {
            const formData = new FormData(this);
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            if (csrfToken) {
                formData.append('_token', csrfToken);
            }

            console.log('Form data:', Object.fromEntries(formData));

            const response = await fetch('/api/my-reservation', {
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
                throw new Error(data.message || 'Erreur lors de la recherche');
            }

            if (data.type === "success") {
                            // ✅ Message de succès
                Swal.fire({
                    icon: 'success',
                    title: 'Réservation trouvée',
                    text: data.message,
                    timer: 5000,
                    showConfirmButton: false,
                    position: 'center',
                    toast: true
                });

                this.reset();
                setTimeout(() => {
                    window.location.href = data.urlback;
                }, 3000);
            }else if(data.type === "error"){
                // ✅ Message d'erreur
                Swal.fire({
                    icon: 'error',
                    title: 'Réservation introuvable',
                    text: data.message,
                    showConfirmButton: true,
                    confirmButtonText: 'Réessayer'
                });
            }

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
