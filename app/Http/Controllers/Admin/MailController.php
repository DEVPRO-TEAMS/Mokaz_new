<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\NotificationPartenaire;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function sendMail()
    {
        $nom = "Jean-Marie";
        $message = "Votre compte a été créé avec succès.";
        $emailSubject = "Bienvenue ";

        $emailData = [
            'nom' => $nom,
            'message' => $message,
            'url' => env('APP_URL') . '/login',
            'buttonText' => 'Finaliser la création du compte',
        ];

        Mail::to('ndouajm@gmail.com')->send(new NotificationPartenaire($emailData, $emailSubject));

        return response()->json([
            'type' => 'success',
            'message' => 'Email envoyé',
            'code' => 200
        ]);
    }
}
