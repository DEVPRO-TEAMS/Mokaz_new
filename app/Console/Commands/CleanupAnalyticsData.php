<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Visit;
use App\Models\VisitHistorique;
use App\Models\PageView;
use App\Models\PageViewHistorique;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupAnalyticsData extends Command
{
    protected $signature = 'analytics:cleanup 
                            {--days=730 : Supprimer les données de plus de X jours (défaut: 730)}
                            {--dry-run : Affiche ce qui serait supprimé sans effectuer les modifications}
                            {--force : Force l\'exécution sans confirmation}';
    
    protected $description = 'Nettoie les données analytiques anciennes pour maintenir la base de données performante';
    
    private $isDryRun = false;
    private $cutoffDate;
    private $stats = [
        'visits_deleted' => 0,
        'sessions_deleted' => 0,
        'page_views_deleted' => 0,
        'page_historiques_deleted' => 0
    ];

    public function handle()
    {
        $this->isDryRun = $this->option('dry-run');
        $days = (int) $this->option('days');
        $this->cutoffDate = Carbon::now()->subDays($days);
        
        $this->info("🚀 Démarrage du nettoyage des données analytiques...");
        $this->newLine();
        
        if ($this->isDryRun) {
            $this->warn("⚠️  MODE DRY RUN - Aucune donnée ne sera supprimée");
            $this->newLine();
        }
        
        $this->info("📅 Les données antérieures au {$this->cutoffDate->format('d/m/Y')} seront nettoyées");
        $this->newLine();
        
        // Afficher les statistiques avant nettoyage
        $this->displayCurrentStats();
        
        // Demander confirmation
        if (!$this->isDryRun && !$this->option('force') && !$this->confirm("Voulez-vous vraiment supprimer les données de plus de {$days} jours ?")) {
            $this->info('❌ Opération annulée.');
            return;
        }
        
        // Exécuter le nettoyage
        $this->performCleanup();
        
        // Afficher les résultats
        $this->displayResults();
        
        if (!$this->isDryRun) {
            $this->info('✅ Nettoyage des données terminé avec succès !');
            
            // Optimiser les tables
            $this->optimizeTables();
        } else {
            $this->info('📋 Simulation terminée. Aucune donnée supprimée.');
        }
    }
    
    private function performCleanup()
    {
        $this->info('1. 🗑️  Nettoyage des historiques de pages...');
        $this->cleanupPageHistoriques();
        $this->newLine();
        
        $this->info('2. 🗑️  Nettoyage des pages vues...');
        $this->cleanupPageViews();
        $this->newLine();
        
        $this->info('3. 🗑️  Nettoyage des sessions...');
        $this->cleanupSessions();
        $this->newLine();
        
        $this->info('4. 🗑️  Nettoyage des visites...');
        $this->cleanupVisits();
        $this->newLine();
    }
    
    private function cleanupPageHistoriques()
    {
        // Supprimer les historiques de page associés à des pages vues anciennes
        $query = PageViewHistorique::whereHas('pageView.visit.historiques', function($q) {
            $q->where('started_at', '<', $this->cutoffDate);
        });
        
        if ($this->isDryRun) {
            $count = $query->count();
            $this->line("     {$count} historiques de page seraient supprimés");
            return;
        }
        
        $count = $query->delete();
        $this->stats['page_historiques_deleted'] = $count;
        $this->line("     {$count} historiques de page supprimés");
    }
    
    private function cleanupPageViews()
    {
        // Supprimer les pages vues associées à des visites anciennes
        $query = PageView::whereHas('visit.historiques', function($q) {
            $q->where('started_at', '<', $this->cutoffDate);
        });
        
        if ($this->isDryRun) {
            $count = $query->count();
            $this->line("     {$count} pages vues seraient supprimées");
            return;
        }
        
        $count = $query->delete();
        $this->stats['page_views_deleted'] = $count;
        $this->line("     {$count} pages vues supprimées");
    }
    
    private function cleanupSessions()
    {
        // Supprimer les sessions anciennes
        $query = VisitHistorique::where('started_at', '<', $this->cutoffDate);
        
        if ($this->isDryRun) {
            $count = $query->count();
            $this->line("     {$count} sessions seraient supprimées");
            return;
        }
        
        $count = $query->delete();
        $this->stats['sessions_deleted'] = $count;
        $this->line("     {$count} sessions supprimées");
    }
    
    private function cleanupVisits()
    {
        // Supprimer les visites qui n'ont plus de sessions
        $query = Visit::doesntHave('historiques');
        
        if ($this->isDryRun) {
            $count = $query->count();
            $this->line("     {$count} visites orphelines seraient supprimées");
            return;
        }
        
        $count = $query->delete();
        $this->stats['visits_deleted'] = $count;
        $this->line("     {$count} visites orphelines supprimées");
    }
    
    private function displayCurrentStats()
    {
        $totalSessions = VisitHistorique::count();
        $oldSessions = VisitHistorique::where('started_at', '<', $this->cutoffDate)->count();
        $percentage = $totalSessions > 0 ? round(($oldSessions / $totalSessions) * 100, 1) : 0;
        
        $this->info('📊 Statistiques actuelles :');
        $this->line("   Sessions totales: {$totalSessions}");
        $this->line("   Sessions anciennes: {$oldSessions} ({$percentage}%)");
        $this->line("   Pages vues totales: " . PageView::count());
        $this->line("   Historiques de page: " . PageViewHistorique::count());
        $this->newLine();
    }
    
    private function displayResults()
    {
        $this->info('📈 Résultats du nettoyage :');
        
        if (array_sum($this->stats) === 0) {
            $this->line('   Aucune donnée à nettoyer !');
            return;
        }
        
        foreach ($this->stats as $type => $count) {
            if ($count > 0) {
                $label = $this->getStatLabel($type);
                $this->line("   {$label}: {$count}");
            }
        }
        
        $this->newLine();
        $this->info('📊 Statistiques après nettoyage :');
        $this->line('   Visites totales: ' . Visit::count());
        $this->line('   Sessions totales: ' . VisitHistorique::count());
        $this->line('   Sessions valides: ' . VisitHistorique::validSessions()->count());
    }
    
    private function optimizeTables()
    {
        $this->info('🔄 Optimisation des tables...');
        
        $tables = ['visits', 'visit_historiques', 'page_views', 'page_view_historiques'];
        
        foreach ($tables as $table) {
            try {
                DB::statement("OPTIMIZE TABLE {$table}");
                $this->line("     Table {$table} optimisée");
            } catch (\Exception $e) {
                $this->warn("     Impossible d'optimiser la table {$table}: " . $e->getMessage());
            }
        }
    }
    
    private function getStatLabel($type)
    {
        $labels = [
            'visits_deleted' => 'Visites supprimées',
            'sessions_deleted' => 'Sessions supprimées',
            'page_views_deleted' => 'Pages vues supprimées',
            'page_historiques_deleted' => 'Historiques de page supprimés'
        ];
        
        return $labels[$type] ?? $type;
    }
}