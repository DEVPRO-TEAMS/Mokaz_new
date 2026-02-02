<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\PageView;
use Illuminate\Support\Str;
use App\Models\PageViewHistorique;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// class TrackPageView
// {

//     public function handle($request, Closure $next)
//     {
//         if (!session()->has('visit_uuid')) {
//             return $next($request);
//         }

//         $url = '/' . ltrim($request->path(), '/');

//         // 🔹 Ignorer les fichiers statiques et la route de tracking
//         foreach (['storage/', 'track/', 'assets/', 'api/'] as $prefix) {
//             if (str_starts_with($url, '/' . $prefix)) {
//                 return $next($request);
//             }
//         }

//         $pageType = $this->resolvePageType($request->path());

//         // 🔹 PageView (statique)
//         $pageView = PageView::firstOrCreate(
//             [
//                 'visit_uuid' => session('visit_uuid'),
//                 'url'        => $url,
//                 'page_type'  => $pageType,
//             ],
//             [
//                 'uuid' => (string) Str::uuid(),
//             ]
//         );

//         // 🔹 Auto-fermeture des historiques inactifs depuis > 30 min
//         PageViewHistorique::where('pageview_uuid', $pageView->uuid)
//             ->whereNull('ended_at')
//             ->where('started_at', '<', now()->subMinutes(30))
//             ->update([
//                 'ended_at' => now(),
//                 'duration' => DB::raw('TIMESTAMPDIFF(SECOND, started_at, NOW())')
//             ]);

//         // 🔹 Toujours créer un nouvel historique
//         $historique = PageViewHistorique::create([
//             'uuid'          => (string) Str::uuid(),
//             'pageview_uuid' => $pageView->uuid,
//             'started_at'    => now(),
//         ]);

//         // ✅ Stocker l'UUID de l'historique actif pour sendBeacon
//         session(['current_page_view_historique_uuid' => $historique->uuid]);

//         return $next($request);
//     }


//     private function resolvePageType(string $path): string
//     {
//         if ($path === '/') return 'accueil';
//         if (str_starts_with($path, 'detail/appartement/')) return 'appartement_detail';
//         if (str_starts_with($path, 'appart-by-property/')) return 'appartement_by_property';
//         if ($path === 'all-apparts') return 'appartement_list';
//         if (str_starts_with($path, 'reservation/detail/')) return 'reservation_detail';
//         if ($path === 'confidentialite') return 'confidentialite';

//         return 'other';
//     }

// }

class TrackPageView
{
    private int $pageTimeout = 30; // minutes
    
    public function handle($request, Closure $next)
    {
        if (!$this->shouldTrack($request)) {
            return $next($request);
        }

        $url = '/' . ltrim($request->path(), '/');
        $pageType = $this->resolvePageType($request->path());

        // PageView (statique)
        $pageView = PageView::firstOrCreate(
            [
                'visit_uuid' => session('visit_uuid'),
                'url'        => $url,
                'page_type'  => $pageType,
            ],
            [
                'uuid' => (string) Str::uuid(),
            ]
        );

        // Auto-fermeture des historiques inactifs
        PageViewHistorique::where('pageview_uuid', $pageView->uuid)
            ->whereNull('ended_at')
            ->where('started_at', '<', now()->subMinutes($this->pageTimeout))
            ->update([
                'ended_at' => now(),
                'duration' => DB::raw('TIMESTAMPDIFF(SECOND, started_at, NOW())')
            ]);

        // Créer un nouvel historique
        $historique = PageViewHistorique::create([
            'uuid'          => (string) Str::uuid(),
            'pageview_uuid' => $pageView->uuid,
            'started_at'    => now(),
        ]);

        session(['current_page_view_historique_uuid' => $historique->uuid]);

        return $next($request);
    }

    private function shouldTrack($request): bool
    {
        // Vérifier qu'on a une session de visite
        if (!session()->has('visit_uuid')) {
            return false;
        }

        $path = $request->path();
        
        // Exclure les fichiers et routes API
        $excluded = [
            'storage/*', 'track/*', 'assets/*', 'api/*',
            '*.css', '*.js', '*.png', '*.jpg', '*.jpeg', '*.gif', '*.ico', '*.svg'
        ];
        
        foreach ($excluded as $pattern) {
            if ($request->is($pattern)) {
                return false;
            }
        }
        
        return true;
    }

    private function resolvePageType(string $path): string
    {
        if ($path === '/') return 'accueil';
        if (str_starts_with($path, 'detail/appartement/')) return 'appartement_detail';
        if (str_starts_with($path, 'appart-by-property/')) return 'appartement_by_property';
        if ($path === 'all-apparts') return 'appartement_list';
        if (str_starts_with($path, 'reservation/detail/')) return 'reservation_detail';
        if ($path === 'confidentialite') return 'confidentialite';
        if ($path === 'contact') return 'contact';
        if ($path === 'a-propos') return 'a_propos';

        return 'other';
    }
}
