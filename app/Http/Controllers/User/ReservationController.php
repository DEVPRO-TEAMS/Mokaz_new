<?php

namespace App\Http\Controllers\User;


use Carbon\Carbon;
use App\Models\Partner;
use App\Models\receipt;
use App\Models\Paiement;
use App\Models\Appartement;
use App\Models\Reservation;
use Illuminate\Support\Str;
use App\Models\Tarification;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\reservatierNotifier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;


class ReservationController extends Controller
{
    // Liste des réservations
    public function index(Request $request)
    {

        $query = Reservation::query();

        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%')
                ->orWhere('nom', 'like', '%' . $request->search . '%')
                ->orWhere('prenoms', 'like', '%' . $request->search . '%')
                ->orWhere('phone', 'like', '%' . $request->search . '%')
                ->orWhere('phone', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        // Filtre par date
        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('created_at', [
                $request->date_debut . ' 00:00:00',
                $request->date_fin . ' 23:59:59'
            ]);
        } elseif ($request->filled('date_debut')) {
            $query->where('created_at', '>=', $request->date_debut . ' 00:00:00');
        } elseif ($request->filled('date_fin')) {
            $query->where('created_at', '<=', $request->date_fin . ' 23:59:59');
        }

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        // Filtre par sejour
        if ($request->filled('sejour')) {
            $query->where('sejour', $request->sejour);
        }

        if (Auth::user()->user_type == 'admin') {
            $reservations = $query->where('etat', 'actif')->whereHas('paiement')->orderBy('created_at', 'desc')->get();
        } else {

            $reservations = $query->where('etat', 'actif')->where('partner_uuid', Auth::user()->partner_uuid)->whereHas('paiement')->orderBy('created_at', 'desc')->get();
        }

        return view('reservations.index', compact('reservations'));
    }


    public function store(Request $request)
    {

        DB::beginTransaction();
        try {
            // Génération du code unique
            $code = 'RES-' . strtoupper(Str::random(6));
            $start_time = Carbon::parse($request->start_time)->format('Y-m-d H:i:s');
            $end_time = $request->end_time
                ? Carbon::parse($request->end_time)->format('Y-m-d H:i:s')
                : null;
            
            $visitUuid = session('visit_uuid');

            $still_to_pay = (float) $request->totalPrice - (float) $request->paymentAmount;
            // Création de la réservation
            $reservation = Reservation::create([
                'uuid' => Str::uuid(),
                'code' => $code,
                'visit_uuid' => $visitUuid,
                'nom' => $request->nom,
                'prenoms' => $request->prenoms,
                'email' => $request->email,
                'phone' => $request->phone,
                'appart_uuid' => $request->appart_uuid,
                'property_uuid' => $request->property_uuid,
                'partner_uuid' => $request->partner_uuid,
                'sejour' => $request->isHourly ? 'Heure' : 'Jour',
                'start_time' => $start_time,
                'end_time' => $end_time,
                'nbr_of_sejour' => $request->isHourly ? $request->hours : $request->days,
                'total_price' => $request->totalPrice,
                'unit_price' => $request->unitPrice,
                'still_to_pay' => $still_to_pay,
                'statut_paiement' => 'pending',
                'status' => 'pending',
                'notes' => $request->notes,
                'payment_method' => $request->payment_method,
                'payment_amount' => $request->paymentAmount
            ]);
            // if ($reservation) {
            //     // faire une decrementation du stock de l'appartement
            //     $appartement = Appartement::where('uuid', $request->appart_uuid)->first();
            //     $appartement->nbr_available = (int) $appartement->nbr_available - 1;
            //     $appartement->save();
            // }

            // $partner = Partner::where('uuid', $request->partner_uuid)->first();
            // $phone = $partner->phone;
            // $message = "Bonjour {$partner->raison_social}, vous avez une nouvelle réservation {$reservation->code}. — MOKAZ ";

            // $this->sendSms($phone, $message);

            // Génération du PDF après enregistrement
            $pdfUrl = $this->generateReceiptPDF($reservation);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Réservation enregistrée avec succès',
                'reservation' => $reservation,
                'pdf_url' => $pdfUrl
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function generateReceiptPDF($reservation)
    {
        $directory = 'receipts';
        $externalUploadDir = base_path(env('STORAGE_FILES', '../uploads/'));

        // Créer le dossier s'il n'existe pas
        if (!is_dir($externalUploadDir . $directory)) {
            mkdir($externalUploadDir . $directory, 0755, true);
        }

        $data = [
            'reservation' => $reservation,
            'date' => now()->format('d/m/Y H:i')
        ];

        $pdf = PDF::loadView('reservations.receipt', $data);

        $filename = 'Recu_' . $reservation->code . '_' . $reservation->uuid . '.pdf';
        $filePath = $externalUploadDir . $directory . '/' . $filename;

        $pdf->save($filePath);

        // Enregistrer dans la table receipts
        Receipt::create([
            'uuid' => Str::uuid(),
            'code' => 'REC-' . strtoupper(Str::random(6)),
            'reservation_uuid' => $reservation->uuid,
            'filename' => $filename,
            'filepath' => "storage/files/{$directory}/{$filename}"
        ]);

        return "storage/files/{$directory}/{$filename}";
    }

    public function paiementWaiting($reservation_uuid)
    {
        $reservation = Reservation::where('uuid', $reservation_uuid)->first();
        return view('reservations.paiement-waiting', compact('reservation'));
    }

    public function getPaiementStatus($reservation_code)
    {
        try {
            $paiement = Paiement::where('reservation_code', $reservation_code)->first();

            if ($paiement && $paiement->payment_status == 'paid') {
                $payment_status = $paiement->payment_status;

                $reservation = Reservation::where('code', $reservation_code)->first();
                $reservation->statut_paiement = 'paid';
                $reservation->save();

                // faire une decrementation du stock de l'appartement
                $appartement = Appartement::where('uuid', $reservation->appart_uuid)->first();
                $appartement->nbr_available = (int) $appartement->nbr_available - 1;
                $appartement->save();

                Log::info('Paiement trouvée: ' . json_encode($paiement));
                $partner = Partner::where('uuid', $reservation->partner_uuid)->first();
                // Récupérer les 10 derniers chiffres du numéro
                $last10 = substr(preg_replace('/\D/', '', $partner->phone), -10);

                // Ajouter l'indicatif "225"
                $phone = "225" . $last10;

                // $phone = "225" .;
                $message = "Bonjour {$partner->raison_social}, vous avez une nouvelle réservation {$reservation_code}. — MOKAZ ";

                $this->sendSms($phone, $message);
                return response()->json([
                    'success' => true,
                    'payment_status' => $payment_status,
                    'message' => 'Paiement trouvé',
                ]);
            } else {
                Log::info('Paiement introuvable pour reservation_code: ' . $reservation_code);
                return response()->json([
                    'success' => false,
                    'payment_status' => null,
                    'message' => 'Paiement introuvable'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la recherche du paiement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function paiementSuccess($reservation_uuid)
    {
        $reservation = Reservation::where('uuid', $reservation_uuid)->first();
        return view('reservations.paiement-success', compact('reservation'));
    }

    public function paiementFailed($reservation_uuid)
    {
        $reservation = Reservation::where('uuid', $reservation_uuid)->first();
        return view('reservations.paiement-failed', compact('reservation'));
    }

    public function getPaiementData(Request $request)
    {
        DB::beginTransaction();
        try {
            $payment_status = ($request->payment_status == 200) ? 'paid' : 'unpaid';
            $paiement = Paiement::create([
                'uuid' => Str::uuid(),
                'code' => 'PAI-' . strtoupper(Str::random(6)),
                'reservation_code' => $request->command_number,
                'payment_mode' => $request->payment_mode,
                'paid_sum' => $request->paid_sum,
                'paid_amount' => $request->paid_amount,
                'payment_token' => $request->payment_token,
                'payment_status' => $payment_status,
                'command_number' => $request->command_number,
                'payment_validation_date' => $request->payment_validation_date
            ]);
            DB::commit();
            return response()->json([
                'success' => true,
                'payment_status' => $payment_status,
                'message' => 'Paiement enregistré',
                'data' => $paiement
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function downloadReceipt($uuid)
    {
        $reservation = Reservation::with('receipt')->where('uuid', $uuid)->firstOrFail();
        // $reservation->statut_paiement = 'paid';
        // $reservation->save();
        $directory = 'receipts/';
        if (!$reservation->receipt) {
            $this->generateReceiptPDF($reservation);
            $reservation->load('receipt');
        } else {
            // supprimer la ligne et le fichier PDF existant et enregistrer un nouveau
            $reservation->receipt->delete();
            $this->generateReceiptPDF($reservation);
        }

        $externalStorageDir = base_path(env('STORAGE_FILES', '../uploads/'));
        $fileFullPath = $externalStorageDir . $directory . $reservation->receipt->filename;

        if (!file_exists($fileFullPath)) {
            return response()->json([
                'success' => false,
                'message' => 'Fichier PDF introuvable'
            ], 404);
        }

        return response()->download($fileFullPath, $reservation->receipt->filename, [
            'Content-Type' => 'application/pdf'
        ]);
    }

    public function myReservation(Request $request)
    {
        $paiement = Paiement::where('reservation_code', $request->code)->first();
        if (!$paiement) {
            return response()->json([
                'type' => 'error',
                'code' => 404,
                'message' => 'Aucune réservation trouvée pour ce code',
                'urlback' => '',
            ]);
        }
        $reservation = Reservation::where('code', $request->code)->first();
        return response()->json([
            'type' => 'success',
            'message' => 'Réservation trouvée. Redirection en cours...',
            'data' => $reservation,
            'urlback' => route('reservation.detail', ['reservation_uuid' => $reservation->uuid]),
            'code' => 200
        ]);
    }

    public function reservationDetail($reservation_uuid)
    {
        $reservation = Reservation::where('uuid', $reservation_uuid)->first();
        return view('reservations.detail', compact('reservation'));
    }
    

    public function confirmReservation($uuid)
    {
        DB::beginTransaction();
        try {
            $reservation = Reservation::where('uuid', $uuid)->first();

            if (!$reservation) {
                return response()->json([
                    'type' => 'error',
                    'success' => false,
                    'code' => 404,
                    'message' => 'Reservation introuvable',
                    'urlback' => '',
                ]);
            }

            $reservation->status = 'confirmed';
            $reservation->traited_by = Auth::user()->uuid;
            $reservation->traited_at = now();

            $reservation->save();


            // envoie de sms 
            // Récupérer les 10 derniers chiffres du numéro
            $last10 = substr(preg_replace('/\D/', '', $reservation->phone), -10);

            // Ajouter l'indicatif "225"
            $phone = "225" . $last10;
            // $phone = $reservation->phone;
            $message = "Bonjour, votre réservation {$reservation->code} a été confirmée. Merci pour votre confiance. — MOKAZ";

            $this->sendSms($phone, $message);

            // Log::info('Reservation confirmed: ' . $reservation->code);
            // Use a proper Blade view for the email content
            $emailSubject = "✅ Réservation Confirmée";
            $emailContent = view('mail.confirm_reservation', [
                'reservation' => $reservation
            ])->render();

            $emailData = [
                'title' => 'de votre réservation - MOKAZ',
                'message' => $emailContent,
                'status' => $reservation->status,
                'code' => $reservation->code,
                'url' => url($reservation->receipt->filepath ?? '#'),
                'buttonText' => 'Télécharger le reçu',
            ];

            Mail::to($reservation->email)->send(new reservatierNotifier($emailData, $emailSubject));


            DB::commit();

            return response()->json([
                'type' => 'success',
                'success' => true,
                'code' => 200,
                'urlback' => 'back',
                'message' => 'La reservation a bien ete confirmer',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Reservation confirmation failed: ' . $e->getMessage());

            return response()->json([
                'type' => 'error',
                'success' => false,
                'code' => 500,
                'urlback' => '',
                'message' => 'Une erreur est survenue lors de la confirmation de la reservation',
            ]);
        }
    }

    // Afficher une réservation
    // public function show($id)
    // {
    //     $reservation = Reservation::find($id);
    //     if (!$reservation) {
    //         return response()->json(['success' => false, 'message' => 'Réservation non trouvée'], 404);
    //     }

    //     return response()->json(['success' => true, 'data' => $reservation]);
    // }
    public function show($uuid)
    {
        $reservation = Reservation::where('uuid', $uuid)->first();
        if (!$reservation) {
            return response()->json(['success' => false, 'message' => 'Réservation non trouvée'], 404);
        }

        return view('reservations.show', compact('reservation'));
    }

    // public function autoRemiseReservation()
    // {
    //     DB::beginTransaction();
    //     try {
    //         $now = Carbon::now();
    //         $results = []; // ✅ Initialisation ici

    //         // On récupère toutes les réservations encore "pending" ou "confirmed"
    //         $reservationsHavingPaiement = Reservation::whereIn('status', ['pending', 'confirmed'])
    //             ->whereHas('paiement')
    //             ->with('appartement')
    //             ->get();

    //         $reservationsDoesntHavePaiement = Reservation::whereIn('status', ['pending', 'confirmed'])
    //             ->whereDoesntHave('paiement')
    //             ->with('appartement')
    //             ->get();

    //         foreach ($reservationsDoesntHavePaiement as $reservation) {
    //             $start = Carbon::parse($reservation->start_time);
    //             $end = Carbon::parse($reservation->end_time);

    //             // 1️ Vérifier le "no show" (10% du séjour écoulé sans l'arrivée du client)
    //             $totalDurationMinutes = $start->diffInMinutes($end);
    //             $threshold = $start->copy()->addMinutes($totalDurationMinutes * 0.1);

    //             if ($now->greaterThan($threshold) && $reservation->is_present == false) {
    //                 // Supprimer la reservation
    //                 $reservation->delete();

    //                 Log::info('Reservation supprimée: ' . $reservation->code . ' aucun paiement et No show ');
    //                 continue;
    //             }

    //             // 2️ Vérifier si le séjour est terminé
    //             if ($now->greaterThanOrEqualTo($end) && $reservation->is_present == true) {
    //                 // Supprimer la reservation
    //                 $reservation->delete();

    //                 Log::info('Reservation supprimée: ' . $reservation->code . ' aucun paiement et finished ');
    //                 continue;
    //             }
    //         }
    //         foreach ($reservationsHavingPaiement as $reservation) {
    //             $start = Carbon::parse($reservation->start_time);
    //             $end = Carbon::parse($reservation->end_time);

    //             // 1️ Vérifier le "no show" (10% du séjour écoulé sans l'arrivée du client)
    //             $totalDurationMinutes = $start->diffInMinutes($end);
    //             $threshold = $start->copy()->addMinutes($totalDurationMinutes * 0.1);

    //             if ($now->greaterThan($threshold) && $reservation->is_present == false) {
    //                 $this->releaseAppartement($reservation, "no_show");

    //                 // $message = "Bonjour, votre réservation N° RES-4MDLGQ a été annulée. Merci de votre compréhension. - " . env('APP_NAME');
    //                 $message = "Bonjour, votre réservation {$reservation->code} a été annulée. Merci de votre compréhension. — MOKAZ";

    //                 // Récupérer les 10 derniers chiffres du numéro
    //                 $last10 = substr(preg_replace('/\D/', '', $reservation->phone), -10);

    //                 // Ajouter l'indicatif "225"
    //                 $phone = "225" . $last10;
    //                 $this->sendSms($phone, $message);


    //                 $emailSubject = "❌ Réservation annulée";
    //                 $emailContent = view('mail.cancel_reservation', [
    //                     'reservation' => $reservation
    //                 ])->render();

    //                 $emailData = [
    //                     'title' => 'Annulation de votre réservation - MOKAZ',
    //                     'message' => $emailContent,
    //                     'status' => $reservation->status,
    //                     'code' => $reservation->code,
    //                     'url' => url($reservation->receipt->filepath ?? '#'),
    //                     'buttonText' => 'Télécharger le reçu',
    //                 ];

    //                 Mail::to($reservation->email)->send(new reservatierNotifier($emailData, $emailSubject));
    //                 Log::info('Reservation auto remise: No show ');
    //                 $results[] = [
    //                     'reservation' => $reservation->code,
    //                     'status' => 'cancelled',
    //                     'message' => 'Réservation annulée (no-show)'
    //                 ];
    //                 continue;
    //             }

    //             // 2️ Vérifier si le séjour est terminé
    //             if ($now->greaterThanOrEqualTo($end) && $reservation->is_present == true) {
    //                 $this->releaseAppartement($reservation, "finished");
    //                 $emailSubject = "✅ Séjour terminé";
    //                 $emailContent = view('mail.finishe_reservation', [
    //                     'reservation' => $reservation
    //                 ])->render();

    //                 $emailData = [
    //                     'title' => 'Merci pour votre séjour - MOKAZ',
    //                     'message' => $emailContent,
    //                     'status' => $reservation->status,
    //                     'code' => $reservation->code,
    //                     'url' => url('/detail/appartement/' . $reservation->appartement->uuid),
    //                     'buttonText' => "Noter l'hébergement",
    //                 ];

    //                 Mail::to($reservation->email)->send(new reservatierNotifier($emailData, $emailSubject));
    //                 Log::info('Reservation auto remise: Séjour terminé ');
    //                 $results[] = [
    //                     'reservation' => $reservation->code,
    //                     'status' => 'completed',
    //                     'message' => 'Séjour terminé'
    //                 ];
    //                 continue;
    //             }
    //         }

    //         DB::commit();

    //         return response()->json([
    //             'success' => true,
    //             'message' => count($results) > 0
    //                 ? "Certaines réservations ont été mises à jour"
    //                 : "Aucune réservation à libérer",
    //             'details' => $results
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Reservation auto remise failed: ' . $e->getMessage());
    //         return response()->json([
    //             'success' => false,
    //             'message' => "Erreur lors de la remise automatique",
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
    public function autoRemiseReservation()
    {
        DB::beginTransaction();
        try {
            $now = Carbon::now();
            $results = [];

            // Récupérer les réservations avec paiement
            $reservationsHavingPaiement = Reservation::whereIn('status', ['pending', 'confirmed'])
                ->whereHas('paiement')
                ->with('appartement')
                ->get()
                ->map(function ($r) {
                    $r->has_payment = true;
                    return $r;
                });

            // Récupérer les réservations sans paiement
            $reservationsDoesntHavePaiement = Reservation::whereIn('status', ['pending', 'confirmed', 'cancelled'])
                ->whereDoesntHave('paiement')
                ->with('appartement')
                ->get()
                ->map(function ($r) {
                    $r->has_payment = false;
                    return $r;
                });

            // Log::info("Réservation avec paiement: " . json_encode($reservationsHavingPaiement));
            Log::info("Nombre de réservation avec paiement: " . $reservationsHavingPaiement->count());
            // Log::info("Réservation sans paiement: " . json_encode($reservationsDoesntHavePaiement));
            Log::info("Nombre de réservation sans paiement: " . $reservationsDoesntHavePaiement->count());
            // Fusion des deux collections
            $reservations = $reservationsHavingPaiement->merge($reservationsDoesntHavePaiement);

            foreach ($reservations as $reservation) {
                $times = $this->getReservationThreshold($reservation);

                // 1. Cas No Show (10% du temps écoulé sans arrivée)
                if ($now->greaterThan($times['threshold']) && !$reservation->is_present) {

                    if ($reservation->has_payment) {
                        // Annulation avec libération d'appartement
                        $this->releaseAppartement($reservation, "no_show");

                        // Envoi SMS + email
                        $this->notifyCancellation($reservation);

                        Log::info("Réservation {$reservation->code} annulée automatiquement (no-show avec paiement)");

                        $results[] = [
                            'reservation' => $reservation->code,
                            'status' => 'cancelled',
                            'message' => 'Annulée (no-show avec paiement)'
                        ];
                    } else {
                        // Suppression directe si aucun paiement
                        $reservation->delete();
                        Log::info("Réservation {$reservation->code} supprimée automatiquement (no-show sans paiement)");

                        $results[] = [
                            'reservation' => $reservation->code,
                            'status' => 'deleted',
                            'message' => 'Supprimée (no-show sans paiement)'
                        ];
                    }
                    continue;
                }

                // 2. Cas séjour terminé
                if ($now->greaterThanOrEqualTo($times['end']) && $reservation->is_present) {

                    if ($reservation->has_payment) {
                        $this->releaseAppartement($reservation, "finished");

                        $this->notifyFinished($reservation);

                        Log::info("Réservation {$reservation->code} libérée automatiquement (séjour terminé)");

                        $results[] = [
                            'reservation' => $reservation->code,
                            'status' => 'completed',
                            'message' => 'Séjour terminé'
                        ];
                    } else {
                        // Suppression directe si aucun paiement
                        $reservation->delete();
                        Log::info("Réservation {$reservation->code} supprimée automatiquement (séjour terminé sans paiement)");

                        $results[] = [
                            'reservation' => $reservation->code,
                            'status' => 'deleted',
                            'message' => 'Supprimée (séjour terminé sans paiement)'
                        ];
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($results) > 0
                    ? "Certaines réservations ont été mises à jour"
                    : "Aucune réservation à libérer",
                'details' => $results
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Reservation auto remise failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => "Erreur lors de la remise automatique",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculer les seuils temporels de la réservation
     */
    private function getReservationThreshold($reservation)
    {
        $start = Carbon::parse($reservation->start_time);
        $end = Carbon::parse($reservation->end_time);
        $totalMinutes = $start->diffInMinutes($end);
        $threshold = $start->copy()->addMinutes($totalMinutes * 0.1);

        return compact('start', 'end', 'threshold');
    }

    /**
     * Notification annulation (SMS + Email)
     */
    private function notifyCancellation($reservation)
    {
        // SMS
        $cleanPhone = preg_replace('/\D/', '', $reservation->phone);
        $last10 = strlen($cleanPhone) >= 10 ? substr($cleanPhone, -10) : $cleanPhone;
        $phone = "225" . $last10;

        $message = "Bonjour, votre réservation {$reservation->code} a été annulée. Merci de votre compréhension. — " . env('APP_NAME');
        $this->sendSms($phone, $message);

        // Email
        if (!empty($reservation->email)) {
            $emailSubject = "❌ Réservation annulée";
            $emailContent = view('mail.cancel_reservation', [
                'reservation' => $reservation
            ])->render();

            $emailData = [
                'title' => 'Annulation de votre réservation - ' . env('APP_NAME'),
                'message' => $emailContent,
                'status' => $reservation->status,
                'code' => $reservation->code,
                'url' => url($reservation->receipt->filepath ?? '#'),
                'buttonText' => 'Télécharger le reçu',
            ];

            Mail::to($reservation->email)->send(new reservatierNotifier($emailData, $emailSubject));
        }
    }

    /**
     * Notification séjour terminé
     */
    private function notifyFinished($reservation)
    {
        if (!empty($reservation->email)) {
            $emailSubject = "✅ Séjour terminé";
            $emailContent = view('mail.finishe_reservation', [
                'reservation' => $reservation
            ])->render();

            $emailData = [
                'title' => 'Merci pour votre séjour - ' . env('APP_NAME'),
                'message' => $emailContent,
                'status' => $reservation->status,
                'code' => $reservation->code,
                'url' => $reservation->appartement
                    ? url('/detail/appartement/' . $reservation->appartement->uuid)
                    : url('/'),
                'buttonText' => "Noter l'hébergement",
            ];

            Mail::to($reservation->email)->send(new reservatierNotifier($emailData, $emailSubject));
        }
    }
    /**
     * Fonction utilitaire pour remettre un appartement disponible
     */
    private function releaseAppartement($reservation, $reason)
    {
        // Incrémenter le stock de l’appartement
        $appartement = $reservation->appartement;
        if ($appartement) {
            $appartement->nbr_available = (int) $appartement->nbr_available + 1;
            $appartement->save();
        }

        // Supprimer l'attribut temporaire avant la sauvegarde
        unset($reservation->has_payment);
        // Mettre à jour la réservation
        $reservation->status = ($reason == "finished") ? "completed" : "cancelled";
        // $reservation->etat = "libéré";
        $reservation->save();
    }

    // Créer une nouvelle réservation
    public function customerIsPresent(Request $request)
    {
        $reservation = Reservation::where('uuid', $request->reservation_uuid)->first();
        if (!$reservation) {
            return response()->json(['success' => false, 'message' => 'Réservation non trouvée'], 404);
        }

        $reservation->is_present = $request->is_present;
        $reservation->save();

        if ($request->is_present == 1) {
            return response()->json([
                'success' => true,
                'message' => 'Client présent',
                'data' => $reservation
            ]);
        }else{
            return response()->json([
                'success' => true,
                'message' => 'Client absent',
                'data' => $reservation
            ]);
        }
    }

    // Mettre à jour une réservation
    public function update(Request $request, $id)
    {
        $reservation = Reservation::find($id);
        if (!$reservation) {
            return response()->json(['success' => false, 'message' => 'Réservation non trouvée'], 404);
        }

        $reservation->update($request->all());

        return response()->json(['success' => true, 'message' => 'Réservation mise à jour', 'data' => $reservation]);
    }
    public function updateByPaiement(Request $request, $uuid)
    {
        $reservation = Reservation::where('uuid', $uuid)->first();
        if (!$reservation) {
            return response()->json(['success' => false, 'message' => 'Réservation non trouvée'], 404);
        }
        $code = 'RES-' . strtoupper(Str::random(6));
        $reservation->code = $code;
        $reservation->save();

        return response()->json(['success' => true, 'message' => 'Réservation mise à jour', 'reservation' => $reservation]);
    }

    // Supprimer une réservation
    public function destroy($id)
    {
        $reservation = Reservation::find($id);
        if (!$reservation) {
            return response()->json(['success' => false, 'message' => 'Réservation non trouvée'], 404);
        }

        $reservation->delete();
        return response()->json(['success' => true, 'message' => 'Réservation supprimée avec succès']);
    }

    // ✅ Fonction de traitement/validation par l’admin
    public function traiter($id)
    {
        $reservation = Reservation::find($id);
        if (!$reservation) {
            return response()->json(['success' => false, 'message' => 'Réservation non trouvée'], 404);
        }

        $reservation->update([
            'status' => 'confirmed',
            'traited_by' => Auth::id(),
            'traited_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Réservation validée', 'data' => $reservation]);
    }

    private function sendSms($to, $message)
    {

        try {

            if ($to && $message) {
                $response = sendSms($to, $message);
            }


            return response()->json([
                'status' => 'success',
                'to' => $to,
                'message' => $message,
                'api_response' => $response
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending SMS: ' . $e->getMessage());
        }
    }
}
