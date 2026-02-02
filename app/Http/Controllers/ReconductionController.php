<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Partner;
use Barryvdh\DomPDF\PDF;
use App\Models\Appartement;
use App\Models\Reservation;
use Illuminate\Support\Str;
use App\Models\Tarification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\ReconductedReservation;

class ReconductionController extends Controller
{

    // public function reconduiReservation($reservation_uuid)
    // {
    //     $reservation = Reservation::where('uuid', $reservation_uuid)->first();
    //     $apparts = Appartement::where('etat','actif')->get();
    //     $appartUids = $apparts->pluck('uuid')->toArray();
    //     $appartTafication = Tarification::where('etat','actif')->whereIn('appart_uuid', $appartUids)->where('price','>=', $reservation->total_price)->get();


    //     return view('reservations.reconduction.index', compact('reservation','appartTafication'));
    // }

    public function reconduiReservation($reservation_uuid)
    {
        $reservation = Reservation::where('uuid', $reservation_uuid)->firstOrFail();

        $apparts = Appartement::where('etat', 'actif')
            ->whereHas('tarifications', function ($query) use ($reservation) {
                $query->where('etat', 'actif')
                    ->where('price', '>=', $reservation->total_price);
            })
            ->with(['tarifications' => function ($query) use ($reservation) {
                $query->where('etat', 'actif')
                    ->where('price', '>=', $reservation->total_price);
            }])
            ->get();

        return view(
            'reservations.reconduction.index',
            compact('reservation', 'apparts')
        );
    }

    public function show($uuid, $reservation_uuid)
    {

        $appart = Appartement::where('uuid', $uuid)->firstOrFail();
        $reservationOld = Reservation::where('uuid', $reservation_uuid)->firstOrFail();
        // \dd($reservationOld);

        return view('reservations.reconduction.show', compact('appart', 'reservationOld'));
    }

    public function store(Request $request)
    {

        Log::info('request', ['request' => $request->all()]);
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


  
}