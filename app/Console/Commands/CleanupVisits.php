<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\VisitHistorique;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupVisits extends Command
{
    protected $signature = 'visits:cleanup 
                            {--days=30 : Supprimer les visites plus anciennes que X jours}';
    
    public function handle()
    {
        $days = $this->option('days');
        $cutoff = Carbon::now()->subDays($days);
        
        // Fermer les sessions ouvertes trop longtemps
        VisitHistorique::whereNull('ended_at')
            ->where('started_at', '<', $cutoff)
            ->update([
                'ended_at' => now(),
                'duration' => DB::raw('TIMESTAMPDIFF(SECOND, started_at, NOW())')
            ]);
        
        $this->info('Nettoyage terminé.');
    }
}

