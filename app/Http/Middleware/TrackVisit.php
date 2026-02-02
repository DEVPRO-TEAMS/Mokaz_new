<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use App\Models\Visit;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\VisitTracker;
use App\Models\VisitHistorique;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stevebauman\Location\Facades\Location;

// class TrackVisit
// {


//     public function handle(Request $request, Closure $next)
//     {
//         if ($this->shouldTrack($request)) {
//             app(VisitTracker::class)->handle($request);
//         }
        
//         return $next($request);
//     }

//     private function shouldTrack(Request $request): bool
//     {
//         // Exclure certaines routes
//         $excluded = [
//             'horizon*', 'nova*', 'telescope*',
//             'assets/*', 'storage/*', 'api/*', 'track/*', 'sanctum/*',
//         ];
        
//         foreach ($excluded as $pattern) {
//             if ($request->is($pattern)) {
//                 return false;
//             }
//         }
        
//         // Exclure les robots et crawlers
//         $userAgent = $request->userAgent();
//         $crawlers = ['bot', 'crawl', 'spider', 'curl', 'wget'];
        
//         foreach ($crawlers as $crawler) {
//             if (stripos($userAgent, $crawler) !== false) {
//                 return false;
//             }
//         }
        
//         return true;
//     }
// }

class TrackVisit
{
    public function handle(Request $request, Closure $next)
    {
        if ($this->shouldTrack($request)) {
            app(VisitTracker::class)->handle($request);
        }
        
        return $next($request);
    }

    private function shouldTrack(Request $request): bool
    {
        // Routes à exclure
        $excluded = [
            'horizon*', 'nova*', 'telescope*', 'debugbar*',
            'assets/*', 'storage/*', 'api/*', 'track/*', 'sanctum/*',
            '*.css', '*.js', '*.png', '*.jpg', '*.jpeg', '*.gif', '*.ico'
        ];
        
        foreach ($excluded as $pattern) {
            if ($request->is($pattern)) {
                return false;
            }
        }
        
        // Exclure les robots
        $userAgent = $request->userAgent() ?? '';
        $crawlers = [
            'bot', 'crawl', 'spider', 'curl', 'wget', 'python', 
            'java', 'ruby', 'php', 'node', 'go-http', 'okhttp'
        ];
        
        foreach ($crawlers as $crawler) {
            if (stripos($userAgent, $crawler) !== false) {
                return false;
            }
        }
        
        return true;
    }
}


