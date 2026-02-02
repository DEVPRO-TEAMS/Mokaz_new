<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Visit;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\VisitHistorique;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stevebauman\Location\Facades\Location;
use Symfony\Component\Intl\Countries;

// class VisitTracker
// {
//     private int $inactivityTimeout = 30; // minutes

//     public function handle(Request $request)
//     {
//         // Vérifier si déjà en session avec activité récente
//         if ($this->hasActiveSession($request)) {
//             return;
//         }

//         // Trouver ou créer la visite
//         $visit = $this->findOrCreateVisit($request);

//         // Gérer la session/historique
//         $historique = $this->manageHistorique($visit, $request);

//         // Stocker en session
//         $this->storeInSession($visit, $historique);
//     }

//     private function hasActiveSession(Request $request): bool
//     {
//         if (!session()->has(['visit_uuid', 'visit_historique_uuid', 'last_activity'])) {
//             Log::info('Aucune session en cours');
//             return false;
//         }

//         $lastActivity = session('last_activity');

//         // Vérifier l'inactivité
//         if (Carbon::parse($lastActivity)->addMinutes($this->inactivityTimeout)->isPast()) {
//             $this->closeExpiredHistorique();
//             return false;
//         }

//         // Mettre à jour le timestamp d'activité
//         session(['last_activity' => now()]);
//         return true;
//     }

//     private function findOrCreateVisit(Request $request): Visit
//     {
//         $start = now()->startOfDay();
//         $end   = now()->endOfDay();

//         $visit = Visit::where('ip_address', $request->ip())
//             ->where('user_agent', $request->userAgent())
//             ->whereBetween('started_at', [$start, $end])
//             ->first();

//         if ($visit) {
//             Log::info('Visite existante trouvée', ['visit_uuid' => $visit->uuid]);
//             return $visit;
//         }

//         Log::info('Nouvelle visite crée', ['ip_address' => $request->ip(), 'user_agent' => $request->userAgent()]);
//         return Visit::create([
//             'uuid'       => (string) Str::uuid(),
//             'ip_address' => $request->ip(),
//             'user_agent' => $request->userAgent(),
//             'started_at' => now(),
//         ]);
//     }

//     private function manageHistorique(Visit $visit, Request $request): VisitHistorique
//     {
//         $historique = $this->findActiveHistorique($visit);

//         if (!$historique || $this->shouldStartNewSession($historique)) {
//             return $this->createNewHistorique($visit, $request);
//         }

//         // Mettre à jour l'activité
//         $historique->touch();
//         return $historique;
//     }

//     private function findActiveHistorique(Visit $visit): ?VisitHistorique
//     {
//         return VisitHistorique::where('visit_uuid', $visit->uuid)
//             ->whereNull('ended_at')
//             ->first();
//     }

//     private function shouldStartNewSession(VisitHistorique $historique): bool
//     {
//         return $historique->updated_at->addMinutes($this->inactivityTimeout)->isPast();
//     }

//     private function createNewHistorique(Visit $visit, Request $request): VisitHistorique
//     {
//         // 2️⃣ GÉOLOCALISATION
//         $country = $city = $latitude = $longitude = null;
//         if ($position = Location::get($request->ip())) {
//             // $country   = $position->countryName ?? null;
//             $country = Countries::getName($position->countryCode, 'fr') ?? null;
//             $city      = $position->cityName ?? null;
//             $latitude  = $position->latitude ?? null;
//             $longitude = $position->longitude ?? null;
//         }
//         // Fermer l'ancienne session si elle existe
//         VisitHistorique::where('visit_uuid', $visit->uuid)
//             ->whereNull('ended_at')
//             ->update([
//                 'ended_at' => now(),
//                 'duration' => DB::raw('TIMESTAMPDIFF(SECOND, started_at, NOW())')
//             ]);

//         return VisitHistorique::create([
//             'uuid' => Str::uuid(),
//             'visit_uuid' => $visit->uuid,
//             'source' => $this->detectSource($request),
//             'referrer' => $request->headers->get('referer'),
//             'coordornneGPS' => $latitude && $longitude ? "$latitude,$longitude" : null,
//             'country'       => $country,
//             'city'          => $city,
//             'started_at' => now(),
//         ]);
//     }

//     private function storeInSession(Visit $visit, VisitHistorique $historique): void
//     {
//         session([
//             'visit_uuid' => $visit->uuid,
//             'visit_historique_uuid' => $historique->uuid,
//             'last_activity' => now(),
//         ]);
//     }

//     private function closeExpiredHistorique(): void
//     {
//         if ($uuid = session('visit_historique_uuid')) {
//             VisitHistorique::where('uuid', $uuid)
//                 ->whereNull('ended_at')
//                 ->update([
//                     'ended_at' => now(),
//                     'duration' => DB::raw('TIMESTAMPDIFF(SECOND, started_at, NOW())')
//                 ]);
//         }

//         session()->forget(['visit_uuid', 'visit_historique_uuid', 'last_activity']);
//     }


//     private function detectSource(Request $request): string
//     {
//         if ($request->has('utm_source')) {
//             $source = strtolower($request->utm_source);
//             $medium = strtolower($request->utm_medium ?? '');

//             if (in_array($medium, ['cpc', 'ads', 'paid'])) {
//                 return 'ads';
//             }

//             if (in_array($source, ['facebook', 'instagram', 'twitter', 'linkedin', 'tiktok', 'snapchat', 'whatsapp'])) {
//                 return 'social';
//             }

//             if (in_array($source, ['google', 'bing', 'yahoo'])) {
//                 return 'organic';
//             }

//             if ($medium === 'email') {
//                 return 'email';
//             }

//             return 'referral';
//         }

//         $referer = strtolower($request->headers->get('referer', ''));

//         if (str_contains($referer, 'google') || str_contains($referer, 'bing')) {
//             return 'organic';
//         }

//         if (preg_match('/facebook|instagram|twitter|linkedin|tiktok/', $referer)) {
//             return 'social';
//         }

//         return $referer ? 'referral' : 'direct';
//     }
// }

class VisitTracker
{
    private int $inactivityTimeout = 30; // minutes
    private int $maxSessionDuration = 7200; // secondes (2 heures)
    
    public function handle(Request $request): void
    {
        if (!$this->shouldTrack($request)) {
            return;
        }

        // Vérifier si déjà en session avec activité récente
        if ($this->hasActiveSession($request)) {
            return;
        }
        
        // Trouver ou créer la visite
        $visit = $this->findOrCreateVisit($request);
        
        // Gérer la session/historique
        $historique = $this->manageHistorique($visit, $request);
        
        // Stocker en session
        $this->storeInSession($visit, $historique);
    }
    
    private function shouldTrack(Request $request): bool
    {
        // Exclure certaines routes
        $excluded = [
            'horizon*', 'nova*', 'telescope*', 'assets/*', 
            'storage/*', 'api/*', 'track/*', 'sanctum/*', '_debugbar/*'
        ];
        
        foreach ($excluded as $pattern) {
            if ($request->is($pattern)) {
                return false;
            }
        }
        
        // Exclure les robots et crawlers
        $userAgent = $request->userAgent();
        $crawlers = ['bot', 'crawl', 'spider', 'curl', 'wget', 'python', 'java', 'ruby'];
        
        foreach ($crawlers as $crawler) {
            if (stripos($userAgent, $crawler) !== false) {
                Log::info('Robot détecté et exclu', ['user_agent' => $userAgent]);
                return false;
            }
        }
        
        return true;
    }
    
    private function hasActiveSession(Request $request): bool
    {
        if (!session()->has(['visit_uuid', 'visit_historique_uuid', 'last_activity'])) {
            return false;
        }
        
        $lastActivity = session('last_activity');
        
        // Vérifier l'inactivité
        if (Carbon::parse($lastActivity)->addMinutes($this->inactivityTimeout)->isPast()) {
            $this->closeExpiredHistorique();
            return false;
        }
        
        // Mettre à jour le timestamp d'activité
        session(['last_activity' => now()]);
        return true;
    }
    
    private function findOrCreateVisit(Request $request): Visit
    {
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        // Chercher une visite aujourd'hui avec la même IP ET user agent
        $visit = Visit::where('ip_address', $request->ip())
            ->where('user_agent', $request->userAgent())
            ->whereBetween('started_at', [$todayStart, $todayEnd])
            ->first();

        if ($visit) {
            Log::debug('Visite existante trouvée', ['visit_uuid' => $visit->uuid]);
            return $visit;
        }

        Log::info('Nouvelle visite créée', [
            'ip_address' => $request->ip(),
            'user_agent' => substr($request->userAgent(), 0, 200)
        ]);
        
        return Visit::create([
            'uuid'       => (string) Str::uuid(),
            'ip_address' => $request->ip(),
            'user_agent' => substr($request->userAgent(), 0, 500), // Limiter la longueur
            'started_at' => now(),
        ]);
    }
    
    private function manageHistorique(Visit $visit, Request $request): VisitHistorique
    {
        $historique = $this->findActiveHistorique($visit);
        
        if (!$historique || $this->shouldStartNewSession($historique)) {
            return $this->createNewHistorique($visit, $request);
        }
        
        // Mettre à jour l'activité
        $historique->update(['last_activity_at' => now()]);
        return $historique;
    }
    
    private function findActiveHistorique(Visit $visit): ?VisitHistorique
    {
        return VisitHistorique::where('visit_uuid', $visit->uuid)
            ->whereNull('ended_at')
            ->first();
    }
    
    private function shouldStartNewSession(VisitHistorique $historique): bool
    {
        // Nouvelle session si inactif depuis plus de timeout
        if ($historique->last_activity_at && 
            $historique->last_activity_at->addMinutes($this->inactivityTimeout)->isPast()) {
            return true;
        }
        
        // Nouvelle session si durée totale > max
        if ($historique->started_at->addSeconds($this->maxSessionDuration)->isPast()) {
            return true;
        }
        
        return false;
    }
    
    private function createNewHistorique(Visit $visit, Request $request): VisitHistorique
    {
        // Fermer l'ancienne session si elle existe
        $this->closeOldHistorique($visit);
        
        // Géolocalisation
        $country = $city = $latitude = $longitude = null;
        $coordinates = null;
        
        if ($position = Location::get($request->ip())) {
            try {
                $country = Countries::getName($position->countryCode, 'fr') ?? $position->countryName ?? null;
                $city = $position->cityName ?? null;
                $latitude = $position->latitude ?? null;
                $longitude = $position->longitude ?? null;
                
                if ($latitude && $longitude) {
                    $coordinates = "$latitude,$longitude";
                }
            } catch (\Exception $e) {
                Log::warning('Erreur géolocalisation', ['error' => $e->getMessage()]);
            }
        }
        
        return VisitHistorique::create([
            'uuid' => (string) Str::uuid(),
            'visit_uuid' => $visit->uuid,
            'source' => $this->detectSource($request),
            'referrer' => $request->headers->get('referer'),
            'coordinates' => $coordinates,
            'country' => $country,
            'city' => $city,
            'started_at' => now(),
            'last_activity_at' => now(),
        ]);
    }
    
    private function closeOldHistorique(Visit $visit): void
    {
        $oldHistorique = VisitHistorique::where('visit_uuid', $visit->uuid)
            ->whereNull('ended_at')
            ->first();
            
        if ($oldHistorique) {
            $duration = now()->diffInSeconds($oldHistorique->started_at);
            $duration = min($duration, $this->maxSessionDuration);
            
            $oldHistorique->update([
                'ended_at' => now(),
                'duration' => $duration,
            ]);
            
            Log::debug('Ancienne session fermée', [
                'uuid' => $oldHistorique->uuid,
                'duration' => $duration
            ]);
        }
    }
    
    private function storeInSession(Visit $visit, VisitHistorique $historique): void
    {
        session([
            'visit_uuid' => $visit->uuid,
            'visit_historique_uuid' => $historique->uuid,
            'last_activity' => now(),
            'session_started_at' => now()->timestamp,
        ]);
    }
    
    private function closeExpiredHistorique(): void
    {
        if ($uuid = session('visit_historique_uuid')) {
            $historique = VisitHistorique::where('uuid', $uuid)
                ->whereNull('ended_at')
                ->first();
                
            if ($historique) {
                $duration = now()->diffInSeconds($historique->started_at);
                $duration = min($duration, $this->maxSessionDuration);
                
                $historique->update([
                    'ended_at' => now(),
                    'duration' => $duration,
                ]);
                
                Log::debug('Session expirée fermée', [
                    'uuid' => $uuid,
                    'duration' => $duration
                ]);
            }
        }

        session()->forget([
            'visit_uuid', 
            'visit_historique_uuid', 
            'last_activity',
            'session_started_at'
        ]);
    }
    
    private function detectSource(Request $request): string
    {
        if ($request->has('utm_source')) {
            $source = strtolower($request->utm_source);
            $medium = strtolower($request->utm_medium ?? '');

            if (in_array($medium, ['cpc', 'ads', 'paid'])) {
                return 'ads';
            }

            if (in_array($source, ['facebook', 'instagram', 'twitter', 'linkedin', 'tiktok'])) {
                return 'social';
            }

            if (in_array($source, ['google', 'bing', 'yahoo', 'duckduckgo'])) {
                return 'organic';
            }

            if ($medium === 'email') {
                return 'email';
            }

            return 'referral';
        }

        $referer = strtolower($request->headers->get('referer', ''));

        if (str_contains($referer, 'google') || str_contains($referer, 'bing')) {
            return 'organic';
        }

        if (preg_match('/facebook|instagram|twitter|linkedin|tiktok/', $referer)) {
            return 'social';
        }

        return $referer ? 'referral' : 'direct';
    }
}
