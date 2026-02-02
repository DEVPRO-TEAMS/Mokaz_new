<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Analytics Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration du système de tracking analytique
    |
    */
    
    'tracking' => [
        // Timeout d'inactivité (en minutes)
        'inactivity_timeout' => env('ANALYTICS_INACTIVITY_TIMEOUT', 30),
        
        // Durée maximale d'une session (en secondes)
        'max_session_duration' => env('ANALYTICS_MAX_SESSION_DURATION', 7200),
        
        // Durée maximale d'une page (en secondes)
        'max_page_duration' => env('ANALYTICS_MAX_PAGE_DURATION', 1800),
        
        // Exclure ces user agents du tracking
        'excluded_user_agents' => [
            'bot', 'crawl', 'spider', 'curl', 'wget', 'python', 'java',
            'ruby', 'php', 'node', 'go-http', 'okhttp', 'library', 'feed',
            'rss', 'monitor', 'checker', 'validator', 'archive', 'slurp'
        ],
        
        // Routes à exclure du tracking
        'excluded_routes' => [
            'horizon*', 'nova*', 'telescope*', 'debugbar*',
            'assets/*', 'storage/*', 'api/*', 'track/*', 'sanctum/*',
            '*.css', '*.js', '*.png', '*.jpg', '*.jpeg', '*.gif', '*.ico'
        ],
    ],
    
    'cleanup' => [
        // Conserver les données pendant X jours
        'keep_data_days' => env('ANALYTICS_KEEP_DATA_DAYS', 365),
        
        // Exécution automatique du cleanup
        'auto_cleanup' => env('ANALYTICS_AUTO_CLEANUP', true),
    ],
    
    'limits' => [
        // Limite pour considérer une durée comme anormale (secondes)
        'abnormal_session_duration' => 28800, // 8 heures
        
        // Limite pour le taux de rebond (secondes)
        'bounce_time_limit' => 30,
        
        // Durée minimum pour une session valide (secondes)
        'min_session_duration' => 1,
    ],
    
    'performance' => [
        // Optimiser automatiquement les tables
        'auto_optimize' => true,
        
        // Nombre de jours pour les statistiques rapides
        'quick_stats_days' => 30,
    ],
];


// # Simulation de la correction (affiche ce qui serait fait)
// php artisan analytics:fix-data --dry-run

// # Correction complète des données
// php artisan analytics:fix-data --all --force

// # Nettoyage des données de plus d'un an
// php artisan analytics:cleanup --days=365 --force

// # Correction spécifique des durées
// php artisan analytics:fix-data --fix-durations --force

// # Nettoyage des visites orphelines
// php artisan analytics:fix-data --clean-visits --force

// # Simulation du nettoyage
// php artisan analytics:cleanup --dry-run