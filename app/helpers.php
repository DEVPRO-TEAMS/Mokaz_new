<?php

use Illuminate\Support\Facades\Http;


function RefgenerateCode($table, $init, $key)
{
    $latest = $table::latest()->first();
    if (! $latest) {
        return $init . '-00001';
    }

    $string = preg_replace("/[^0-9\.]/", '', $latest->$key);

    return $init . '-' . sprintf('%05d', $string + 1);
}
function Refgenerate($table, $init, $key)
{
    $latest = $table::latest()->first();
    if (! $latest) {
        return $init . '-00001';
    }

    $string = preg_replace("/[^0-9\.]/", '', $latest->$key);

    return $init . '-' . sprintf('%05d', $string + 1);
}

function cleanHtmlForSearch(?string $html): string
{
    if (!$html) {
        return '';
    }

    // 1. Décoder les entités HTML (&eacute; → é)
    $text = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    // 2. Supprimer les balises HTML
    $text = strip_tags($text);

    // 3. Supprimer les espaces multiples
    $text = preg_replace('/\s+/', ' ', $text);

    return trim($text);
}

if (!function_exists('sendSms')) {
    function sendSms($to, $message)
    {
        $apiUrl = config('services.sms.url');
        $apiKey = config('services.sms.key');

        $response = Http::withHeaders([
            'Authorization' => $apiKey,
            'Accept'        => 'application/json',
        ])->post($apiUrl, [
            'from'    => 'jsbeyci',
            'to'      => $to,
            'content' => $message,
        ]);

        return $response->json();
    }
}


if (!function_exists('formatTemps')) {
    function formatTemps($minutes)
    {
        if (!$minutes) {
            return null;
        }
        if ($minutes >= 60) {
            $heures = floor($minutes / 60);
            $mins = round($minutes % 60);
            return $heures . 'h ' . ($mins > 0 ? $mins . 'min' : '');
        }
        return round($minutes) . ' min';
    }
}

if (!function_exists('formatDistance')) {
    function formatDistance($km)
    {
        if (!$km) {
            return null;
        }
        $metres = $km * 1000;
        return $metres >= 1000
            ? number_format($km, 1, ',', ' ') . ' km'
            : number_format($metres, 0, ',', ' ') . ' m';
    }
}
