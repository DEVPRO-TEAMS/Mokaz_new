@extends('layouts.main')
@section('content')
    <style>
        /* body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    background: linear-gradient(135deg, #ffffff 0%, #fef5f5 100%);
                    height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    overflow: hidden;
                } */

        .container-waiting {
            text-align: center;
            background: white;
            padding: 60px 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(220, 38, 127, 0.1);
            max-width: 800px;
            margin: 50px auto;
            width: 90%;
            position: relative;
            border: 2px solid #fee;
        }

        .container-waiting::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #dc2626, #ef4444, #f87171);
            border-radius: 20px 20px 0 0;
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% {
                opacity: 0.5;
            }

            50% {
                opacity: 1;
            }

            100% {
                opacity: 0.5;
            }
        }

        .loading-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 30px;
            position: relative;
        }

        .spinner {
            width: 80px;
            height: 80px;
            border: 6px solid #fee2e2;
            border-top: 6px solid #dc2626;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .checkmark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 40px;
            height: 40px;
            color: #dc2626;
            opacity: 0;
            animation: fadeInOut 3s infinite;
        }

        @keyframes fadeInOut {

            0%,
            70% {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0.8);
            }

            85% {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1.1);
            }

            100% {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0.8);
            }
        }

        h1 {
            color: #dc2626;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 15px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.8;
            }
        }

        .subtitle {
            color: #666;
            font-size: 18px;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: #fee2e2;
            border-radius: 10px;
            margin: 30px 0;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #dc2626, #ef4444);
            border-radius: 10px;
            animation: progressAnimation 4s infinite;
            box-shadow: 0 0 10px rgba(220, 38, 38, 0.3);
        }

        @keyframes progressAnimation {
            0% {
                width: 20%;
            }

            25% {
                width: 45%;
            }

            50% {
                width: 70%;
            }

            75% {
                width: 85%;
            }

            100% {
                width: 95%;
            }
        }

        .status-text {
            color: #dc2626;
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 20px;
            animation: textChange 6s infinite;
        }

        @keyframes textChange {

            0%,
            33% {
                opacity: 1;
            }

            34%,
            66% {
                opacity: 0;
            }

            67%,
            100% {
                opacity: 1;
            }
        }

        .dots {
            display: inline-block;
            animation: dots 1.5s infinite;
        }

        @keyframes dots {

            0%,
            20% {
                content: '';
            }

            40% {
                content: '.';
            }

            60% {
                content: '..';
            }

            80%,
            100% {
                content: '...';
            }
        }

        .security-info {
            background: #fef5f5;
            border: 1px solid #fee2e2;
            border-radius: 12px;
            padding: 20px;
            margin-top: 25px;
        }

        .security-icon {
            color: #dc2626;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .security-text {
            color: #7f1d1d;
            font-size: 14px;
            line-height: 1.5;
        }

        .floating-particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .particle {
            position: absolute;
            width: 6px;
            height: 6px;
            background: #fecaca;
            border-radius: 50%;
            animation: float 8s infinite linear;
        }

        @keyframes float {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }

            10% {
                opacity: 1;
            }

            90% {
                opacity: 1;
            }

            100% {
                transform: translateY(-100vh) rotate(360deg);
                opacity: 0;
            }
        }

        .estimated-time {
            background: white;
            border: 2px solid #fee2e2;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            display: inline-block;
        }

        .time-text {
            color: #dc2626;
            font-weight: 600;
            font-size: 14px;
        }

        /* Animation des particules flottantes */
        .particle:nth-child(1) {
            left: 20%;
            animation-delay: 0s;
        }

        .particle:nth-child(2) {
            left: 40%;
            animation-delay: 2s;
        }

        .particle:nth-child(3) {
            left: 60%;
            animation-delay: 4s;
        }

        .particle:nth-child(4) {
            left: 80%;
            animation-delay: 6s;
        }

        .particle:nth-child(5) {
            left: 10%;
            animation-delay: 1s;
        }

        .particle:nth-child(6) {
            left: 70%;
            animation-delay: 3s;
        }

        .particle:nth-child(7) {
            left: 30%;
            animation-delay: 5s;
        }

        .particle:nth-child(8) {
            left: 90%;
            animation-delay: 7s;
        }

        @media (max-width: 480px) {
            .container-waiting {
                padding: 40px 20px;
            }

            h1 {
                font-size: 24px;
            }

            .subtitle {
                font-size: 16px;
            }

            .loading-icon {
                width: 60px;
                height: 60px;
            }

            .spinner {
                width: 60px;
                height: 60px;
            }

            .checkmark {
                width: 30px;
                height: 30px;
            }
        }
    </style>
    <div class="floating-particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="container-waiting">
        <div class="loading-icon">
            <div class="spinner"></div>
            <div class="checkmark">✓</div>
        </div>

        <h1>Traitement en cours<span class="dots"></span></h1>
        <p class="subtitle">Votre paiement est en cours de validation. Veuillez confirmer la transaction à partir de
            téléphone.</p>

        <div class="progress-bar">
            <div class="progress-fill"></div>
        </div>

        <div class="status-text" id="statusText">Vérification des informations de paiement...</div>

        <div class="estimated-time">
            <div class="time-text">⏱️ Temps estimé : 30-60 secondes</div>
        </div>

        <div class="security-info">
            <div class="security-icon">🔒</div>
            <div class="security-text">
                <strong>Transaction sécurisée</strong><br>
                Vos données sont protégées par un cryptage SSL 256 bits.
                Ne fermez pas cette page pendant le traitement.
            </div>
        </div>
    </div>


    <script>
        // Récupération des données réservation
        const reservationData = @json($reservation) || {};
        const reservationUuid = reservationData.uuid || '';

        // Messages animés
        const statusMessages = [
            'Vérification des informations de paiement...',
            'Confirmation auprès de votre opérateur...',
            'Finalisation de la transaction...',
            'Génération de votre reçu...'
        ];

        let messageIndex = 0;
        const statusElement = document.getElementById('statusText');

        function updateStatus() {
            statusElement.style.opacity = '0';

            setTimeout(() => {
                statusElement.textContent = statusMessages[messageIndex];
                statusElement.style.opacity = '1';
                messageIndex = (messageIndex + 1) % statusMessages.length;
            }, 500);
        }

        // Changer le message toutes les 3 secondes
        setInterval(updateStatus, 3000);

        // Gestion du cron job
        let isJobRunning = false;
        let startTime = Date.now(); // début du suivi
        const TIMEOUT = 3 * 60 * 1000; // 3 minutes en ms
        let cronInterval = null; // identifiant de l'intervalle

        function executeCronJob() {
            if (isJobRunning) {
                console.log("La tâche cron est déjà en cours.");
                return;
            }

            isJobRunning = true;

            axios.post("/api/cron/get-paiement-status/" + reservationData.code, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => {
                    console.log('Cron job executed successfully:', response.data);

                    if (response.data.payment_status === 'paid') {
                        clearInterval(cronInterval); // on arrête la boucle
                        Swal.fire({
                            title: 'Paiement Réussi !',
                            text: 'Redirection...',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 3000
                        });
                        setTimeout(() => {
                            window.location.href = '/reservation/paiement-success/' + reservationUuid;
                        }, 1500);
                    } else {
                        // Vérifier si délai dépassé
                        let elapsed = Date.now() - startTime;
                        if (elapsed >= TIMEOUT) {
                            clearInterval(cronInterval); // stop la boucle
                            Swal.fire({
                                title: 'Échec du Paiement',
                                text: "Le paiement n'a pas pu être confirmé dans le temps imparti.",
                                icon: 'error',
                                showConfirmButton: true,
                            }).then(() => {
                                window.location.href = '/reservation/paiement-failed/' + reservationUuid;
                            });
                        }
                    }
                })
                .catch(error => {
                    console.error("Erreur lors de l'exécution du cron :", error);
                })
                .finally(() => {
                    isJobRunning = false;
                });
        }

        // Exécuter toutes les 10 secondes tant que les 3 minutes ne sont pas passées
        cronInterval = setInterval(executeCronJob, 10000);


        // // Gestion du cron job
        // let isJobRunning = false;
        // let startTime = Date.now(); // début du suivi
        // const TIMEOUT = 3 * 60 * 1000; // 3 minutes en ms
        // function executeCronJob() {
        //     if (!isJobRunning) {
        //         isJobRunning = true;

        //         axios.post("/api/cron/get-paiement-status/" + reservationData.code, {
        //                 // headers: {
        //                 //     'Accept': 'application/json',
        //                 // },
        //                 headers: {
        //                     'X-CSRF-TOKEN': '{{ csrf_token() }}',
        //                     'Accept': 'application/json',
        //                     'Content-Type': 'application/json',
        //                 },
        //             })
        //             .then(response => {
        //                 console.log('Cron job executed successfully:', response.data.message);

        //                 if (response.data.payment_status === 'paid') {
        //                     // Paiement validé
        //                     setTimeout(() => {
        //                         Swal.fire({
        //                             title: 'Paiement Réussi !',
        //                             text: 'Redirection...',
        //                             icon: 'success',
        //                             showConfirmButton: false,
        //                             timer: 3000
        //                         });
        //                         window.location.href = '/reservation/paiement-success/' + reservationUuid;
        //                     }, 1500);
        //                 } else {
        //                     // Vérifier si délai dépassé
        //                     let elapsed = Date.now() - startTime;
        //                     if (elapsed >= TIMEOUT) {
        //                         Swal.fire({
        //                             title: 'Échec du Paiement',
        //                             text: "Le paiement n'a pas pu être confirmé dans le temps imparti.",
        //                             icon: 'error',
        //                             showConfirmButton: true,
        //                         }).then(() => {
        //                             window.location.href = '/reservation/paiement-failed/' + reservationUuid;
        //                         });
        //                     }
        //                 }
        //             })
        //             .catch(error => {
        //                 console.error("Erreur lors de l'exécution du cron :", error);
        //             })
        //             .finally(() => {
        //                 isJobRunning = false;
        //             });
        //     } else {
        //         console.log("La tâche cron est déjà en cours.");
        //     }
        // }


        // // Exécuter toutes les 10 secondes tant que les 3 minutes ne sont pas passées
        // setInterval(executeCronJob, 10000);

        // Empêcher la fermeture accidentelle de la page
        // window.addEventListener('beforeunload', function(e) {
        //     const confirmationMessage =
        //         'Votre paiement est en cours de traitement. Êtes-vous sûr de vouloir quitter cette page ?';
        //     e.returnValue = confirmationMessage;
        //     return confirmationMessage;
        // });

        // Animation du curseur de points (...)
        const dotsElement = document.querySelector('.dots');
        let dotCount = 0;

        setInterval(() => {
            dotCount = (dotCount + 1) % 4;
            dotsElement.textContent = '.'.repeat(dotCount);
        }, 500);
    </script>
@endsection
