<?php

// private function fixCoordinates()
    // {
    //     $this->info('   Formatage des coordonnées GPS...');
        
    //     // Vérifier si la colonne existe (ancien nom)
    //     $hasOldColumn = DB::getSchemaBuilder()->hasColumn('visit_historiques', 'coordornneGPS');
    //     $hasNewColumn = DB::getSchemaBuilder()->hasColumn('visit_historiques', 'coordinates');
        
    //     if ($hasOldColumn && !$hasNewColumn) {
    //         // Renommer la colonne si elle existe avec l'ancien nom
    //         if (!$this->isDryRun) {
    //             DB::statement('ALTER TABLE visit_historiques CHANGE coordornneGPS coordinates VARCHAR(255) NULL');
    //             $this->line("     Colonne 'coordornneGPS' renommée en 'coordinates'");
    //         } else {
    //             $this->line("     Colonne 'coordornneGPS' serait renommée en 'coordinates'");
    //         }
    //     }
        
    //     // Nettoyer les coordonnées invalides
    //     $query = VisitHistorique::whereNotNull('coordinates')
    //         ->where(function($q) {
    //             $q->where('coordinates', 'not like', '%,%')
    //               ->orWhere('coordinates', 'like', '%,%,%')
    //               ->orWhereRaw('LENGTH(coordinates) > 50');
    //         });
        
    //     if ($this->isDryRun) {
    //         $count = $query->count();
    //         $this->line("     {$count} coordonnées invalides seraient nettoyées");
    //         return;
    //     }
        
    //     $count = $query->update(['coordinates' => null]);
    //     $this->stats['coordinates_fixed'] += $count;
    //     $this->line("     {$count} coordonnées invalides nettoyées");
    // }

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Visit;
use App\Models\VisitHistorique;
use App\Models\PageView;
use App\Models\PageViewHistorique;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FixAnalyticsData extends Command
{
    protected $signature = 'analytics:fix-data 
                            {--dry-run : Affiche ce qui serait fait sans effectuer les modifications}
                            {--force : Force l\'exécution sans confirmation}
                            {--clean-visits : Nettoie les visites orphelines}
                            {--clean-sessions : Nettoie les sessions problématiques}
                            {--fix-durations : Corrige les durées aberrantes}
                            {--all : Exécute toutes les corrections}';
    
    protected $description = 'Corrige les données analytiques problématiques et incohérentes';
    
    private $isDryRun = false;
    private $stats = [
        'visits_fixed' => 0,
        'sessions_fixed' => 0,
        'durations_fixed' => 0,
        'robots_removed' => 0,
        'orphaned_removed' => 0,
        'coordinates_fixed' => 0,
        'user_agents_fixed' => 0
    ];

    public function handle()
    {
        $this->isDryRun = $this->option('dry-run');
        
        $this->info('🚀 Démarrage de la correction des données analytiques...');
        $this->newLine();
        
        if ($this->isDryRun) {
            $this->warn('⚠️  MODE DRY RUN - Aucune modification ne sera appliquée');
            $this->newLine();
        }
        
        // Afficher les statistiques avant correction
        $this->displayCurrentStats();
        
        // Demander confirmation si pas en mode dry-run ou force
        if (!$this->isDryRun && !$this->option('force') && !$this->confirm('Voulez-vous vraiment corriger les données analytiques ?')) {
            $this->info('❌ Opération annulée.');
            return;
        }
        
        // Exécuter les corrections en fonction des options
        $this->executeCorrections();
        
        // Afficher les résultats
        $this->displayResults();
        
        if (!$this->isDryRun) {
            $this->info('✅ Correction des données terminée avec succès !');
        } else {
            $this->info('📋 Simulation terminée. Aucune modification appliquée.');
        }
    }
    
    private function executeCorrections()
    {
        $options = $this->options();
        
        // Si --all est spécifié, exécuter toutes les corrections
        if ($options['all'] || (!$options['clean-visits'] && !$options['clean-sessions'] && 
            !$options['fix-durations'] && !$options['all'])) {
            $this->fixAllIssues();
        } else {
            // Exécuter seulement les corrections spécifiées
            if ($options['clean-visits']) {
                $this->cleanOrphanedVisits();
                $this->removeRobotVisits();
                $this->fixUserAgents();
            }
            
            if ($options['clean-sessions']) {
                $this->closeOrphanedSessions();
                $this->fixSessionSources();
            }
            
            if ($options['fix-durations']) {
                $this->fixAbnormalDurations();
                $this->fixPageDurations();
            }
        }
    }
    
    private function fixAllIssues()
    {
        $this->info('1. 🔧 Correction des visites problématiques...');
        $this->cleanOrphanedVisits();
        $this->removeRobotVisits();
        $this->fixUserAgents();
        $this->newLine();
        
        $this->info('2. 🔧 Correction des sessions problématiques...');
        $this->closeOrphanedSessions();
        $this->fixSessionSources();
        $this->fixEmptyCities();
        $this->newLine();
        
        $this->info('3. 🔧 Correction des durées aberrantes...');
        $this->fixAbnormalDurations();
        $this->fixPageDurations();
        $this->newLine();
        
        $this->info('4. 🔧 Correction des coordonnées GPS...');
        $this->fixCoordinates();
        $this->newLine();
        
        $this->info('5. 🔧 Correction des relations orphelines...');
        $this->fixOrphanedRelations();
        $this->newLine();
    }
    
    private function cleanOrphanedVisits()
    {
        $this->info('   Suppression des visites sans sessions...');
        
        // Visites sans aucune session associée
        $query = Visit::doesntHave('historiques');
        
        if ($this->isDryRun) {
            $count = $query->count();
            $this->line("     {$count} visites orphelines seraient supprimées");
            return;
        }
        
        $count = $query->delete();
        $this->stats['orphaned_removed'] += $count;
        $this->line("     {$count} visites orphelines supprimées");
    }
    
    private function removeRobotVisits()
    {
        $this->info('   Suppression des visites de robots...');
        
        $botPatterns = [
            '%bot%', '%crawl%', '%spider%', '%scraper%', '%curl%', '%wget%',
            '%python%', '%java%', '%ruby%', '%php%', '%node%', '%go-http%',
            '%okhttp%', '%library%', '%feed%', '%rss%', '%monitor%', '%checker%',
            '%validator%', '%archive%', '%slurp%', '%collector%', '%fetcher%'
        ];
        
        $query = Visit::where(function($q) use ($botPatterns) {
            foreach ($botPatterns as $pattern) {
                $q->orWhere('user_agent', 'like', $pattern);
            }
        });
        
        if ($this->isDryRun) {
            $count = $query->count();
            $this->line("     {$count} visites robots seraient supprimées");
            return;
        }
        
        $count = $query->delete();
        $this->stats['robots_removed'] += $count;
        $this->line("     {$count} visites robots supprimées");
    }
    
    private function fixUserAgents()
    {
        $this->info('   Normalisation des user_agent trop longs...');
        
        $query = Visit::whereRaw('LENGTH(user_agent) > 500');
        
        if ($this->isDryRun) {
            $count = $query->count();
            $this->line("     {$count} user_agent seraient tronqués à 500 caractères");
            return;
        }
        
        $count = $query->update(['user_agent' => DB::raw('SUBSTRING(user_agent, 1, 500)')]);
        $this->stats['user_agents_fixed'] += $count;
        $this->line("     {$count} user_agent tronqués");
    }
    
    private function closeOrphanedSessions()
    {
        $this->info('   Fermeture des sessions orphelines (ouvertes > 24h)...');
        
        $cutoffDate = Carbon::now()->subDay();
        $query = VisitHistorique::whereNull('ended_at')
            ->where('started_at', '<', $cutoffDate);
        
        if ($this->isDryRun) {
            $count = $query->count();
            $this->line("     {$count} sessions orphelines seraient fermées");
            return;
        }
        
        $count = $query->update([
            'ended_at' => DB::raw('DATE_ADD(started_at, INTERVAL 1 HOUR)'),
            'duration' => 3600,
            'last_activity_at' => DB::raw('DATE_ADD(started_at, INTERVAL 1 HOUR)')
        ]);
        
        $this->stats['sessions_fixed'] += $count;
        $this->line("     {$count} sessions orphelines fermées");
    }
    
    private function fixSessionSources()
    {
        $this->info('   Correction des sources de session invalides...');
        
        // Remplacer les sources vides ou invalides par 'direct'
        $query = VisitHistorique::whereNull('source')
            ->orWhere('source', '')
            ->orWhere('source', 'unknown');
        
        if ($this->isDryRun) {
            $count = $query->count();
            $this->line("     {$count} sources invalides seraient corrigées en 'direct'");
            return;
        }
        
        $count = $query->update(['source' => 'direct']);
        $this->stats['sessions_fixed'] += $count;
        $this->line("     {$count} sources corrigées");
    }
    
    private function fixEmptyCities()
    {
        $this->info('   Correction des villes vides...');
        
        $query = VisitHistorique::where(function($q) {
            $q->whereNull('city')
              ->orWhere('city', '')
              ->orWhere('city', 'like', '%unknown%');
        })->whereNotNull('country');
        
        if ($this->isDryRun) {
            $count = $query->count();
            $this->line("     {$count} villes vides seraient corrigées");
            return;
        }
        
        $count = $query->update(['city' => 'Inconnue']);
        $this->stats['sessions_fixed'] += $count;
        $this->line("     {$count} villes corrigées");
    }
    
    private function fixAbnormalDurations()
    {
        $this->info('   Correction des durées de session aberrantes...');
        
        // 1. Sessions avec durée > 8h (28800 secondes)
        $query1 = VisitHistorique::whereNotNull('duration')
            ->where('duration', '>', 28800);
        
        if ($this->isDryRun) {
            $count1 = $query1->count();
        } else {
            $count1 = $query1->update(['duration' => 28800]);
        }
        
        // 2. Sessions avec durée négative
        $query2 = VisitHistorique::whereNotNull('duration')
            ->where('duration', '<', 0);
        
        if ($this->isDryRun) {
            $count2 = $query2->count();
        } else {
            $count2 = $query2->update(['duration' => 0]);
        }
        
        // 3. Sessions avec ended_at avant started_at
        $query3 = VisitHistorique::whereNotNull('ended_at')
            ->whereNotNull('started_at')
            ->whereRaw('ended_at < started_at');
        
        if ($this->isDryRun) {
            $count3 = $query3->count();
        } else {
            $count3 = $query3->update([
                'duration' => 0,
                'ended_at' => DB::raw('DATE_ADD(started_at, INTERVAL 1 MINUTE)')
            ]);
        }
        
        $total = $count1 + $count2 + $count3;
        $this->stats['durations_fixed'] += $total;
        
        $this->line("     {$count1} durées > 8h corrigées");
        $this->line("     {$count2} durées négatives corrigées");
        $this->line("     {$count3} dates incohérentes corrigées");
    }
    
    private function fixPageDurations()
    {
        $this->info('   Correction des durées de page aberrantes...');
        
        // Pages avec durée > 30 min (1800 secondes)
        $query = PageViewHistorique::whereNotNull('duration')
            ->where('duration', '>', 1800);
        
        if ($this->isDryRun) {
            $count = $query->count();
            $this->line("     {$count} durées de page > 30min seraient corrigées");
            return;
        }
        
        $count = $query->update(['duration' => 1800]);
        $this->stats['durations_fixed'] += $count;
        $this->line("     {$count} durées de page corrigées");
    }
    
    
    private function fixCoordinates()
{
    $this->info('   Formatage des coordonnées GPS...');

    // Vérifier si la colonne "coordinates" existe
    $hasCoordinates = DB::getSchemaBuilder()->hasColumn('visit_historiques', 'coordinates');

    // Vérifier si la colonne ancienne existe
    $hasOldColumn = DB::getSchemaBuilder()->hasColumn('visit_historiques', 'coordornneGPS');

    if ($hasOldColumn && !$hasCoordinates) {
        if (!$this->isDryRun) {
            DB::statement('ALTER TABLE visit_historiques CHANGE coordornneGPS coordinates VARCHAR(255) NULL');
            $this->line("     Colonne 'coordornneGPS' renommée en 'coordinates'");
            $hasCoordinates = true; // On peut maintenant utiliser la nouvelle colonne
        } else {
            $this->line("     Colonne 'coordornneGPS' serait renommée en 'coordinates'");
        }
    }

    // Si la colonne coordinates n'existe toujours pas, on arrête ici pour éviter l'erreur
    if (!$hasCoordinates) {
        $this->warn("     La colonne 'coordinates' n'existe pas, suppression des coordonnées invalides ignorée");
        return;
    }

    // Nettoyer les coordonnées invalides
    $query = VisitHistorique::whereNotNull('coordinates')
        ->where(function($q) {
            $q->where('coordinates', 'not like', '%,%')
              ->orWhere('coordinates', 'like', '%,%,%')
              ->orWhereRaw('LENGTH(coordinates) > 50');
        });

    if ($this->isDryRun) {
        $count = $query->count();
        $this->line("     {$count} coordonnées invalides seraient nettoyées");
        return;
    }

    $count = $query->update(['coordinates' => null]);
    $this->stats['coordinates_fixed'] += $count;
    $this->line("     {$count} coordonnées invalides nettoyées");
}

    
    private function fixOrphanedRelations()
    {
        $this->info('   Correction des relations orphelines...');
        
        // 1. PageViewHistorique sans PageView parent
        $this->info('     PageViewHistorique orphelins...');
        $query1 = PageViewHistorique::doesntHave('pageView');
        
        if ($this->isDryRun) {
            $count1 = $query1->count();
        } else {
            $count1 = $query1->delete();
        }
        
        // 2. PageView sans Visit parent
        $this->info('     PageView orphelins...');
        $query2 = PageView::doesntHave('visit');
        
        if ($this->isDryRun) {
            $count2 = $query2->count();
        } else {
            $count2 = $query2->delete();
        }
        
        // 3. VisitHistorique sans Visit parent
        $this->info('     VisitHistorique orphelins...');
        $query3 = VisitHistorique::doesntHave('visit');
        
        if ($this->isDryRun) {
            $count3 = $query3->count();
        } else {
            $count3 = $query3->delete();
        }
        
        $total = $count1 + $count2 + $count3;
        $this->stats['orphaned_removed'] += $total;
        
        $this->line("     {$count1} PageViewHistorique orphelins supprimés");
        $this->line("     {$count2} PageView orphelins supprimés");
        $this->line("     {$count3} VisitHistorique orphelins supprimés");
    }
    
    private function displayCurrentStats()
    {
        $this->info('📊 Statistiques actuelles :');
        $this->line('   Visites totales: ' . Visit::count());
        $this->line('   Sessions totales: ' . VisitHistorique::count());
        $this->line('   Sessions actives: ' . VisitHistorique::whereNull('ended_at')->count());
        $this->line('   Sessions > 8h: ' . VisitHistorique::where('duration', '>', 28800)->count());
        $this->line('   Sessions sans fin: ' . VisitHistorique::whereNull('ended_at')
            ->where('started_at', '<', Carbon::now()->subDay())->count());
        $this->line('   Visites robots: ' . Visit::where('user_agent', 'like', '%bot%')
            ->orWhere('user_agent', 'like', '%crawl%')
            ->orWhere('user_agent', 'like', '%spider%')->count());
        $this->newLine();
    }
    
    private function displayResults()
    {
        $this->info('📈 Résultats de la correction :');
        
        if (array_sum($this->stats) === 0) {
            $this->line('   Aucune correction nécessaire !');
            return;
        }
        
        foreach ($this->stats as $type => $count) {
            if ($count > 0) {
                $label = $this->getStatLabel($type);
                $this->line("   {$label}: {$count}");
            }
        }
        
        $this->newLine();
        $this->info('📊 Statistiques après correction :');
        $this->line('   Visites totales: ' . Visit::count());
        $this->line('   Sessions totales: ' . VisitHistorique::count());
        $this->line('   Sessions valides: ' . VisitHistorique::validSessions()->count());
        $this->line('   Sessions actives: ' . VisitHistorique::active()->count());
    }
    
    private function getStatLabel($type)
    {
        $labels = [
            'visits_fixed' => 'Visites corrigées',
            'sessions_fixed' => 'Sessions corrigées',
            'durations_fixed' => 'Durées corrigées',
            'robots_removed' => 'Robots supprimés',
            'orphaned_removed' => 'Éléments orphelins supprimés',
            'coordinates_fixed' => 'Coordonnées corrigées',
            'user_agents_fixed' => 'User agents normalisés'
        ];
        
        return $labels[$type] ?? $type;
    }
}