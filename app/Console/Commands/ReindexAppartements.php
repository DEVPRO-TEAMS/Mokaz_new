<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appartement;
use App\Models\Search;
use Illuminate\Support\Str;
use App\Services\Search\AppartementSearchService;

class ReindexAppartements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reindex:appartements';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Re-indexe tous les appartements existants dans la table Search';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Début du re-indexage des appartements...');

        $appartements = Appartement::with(['property.ville', 'property.pays', 'tarifications', 'property.type', 'property.category'])->where('etat','!=', 'inactif')->get();

        $bar = $this->output->createProgressBar($appartements->count());
        $bar->start();

        foreach ($appartements as $appartement) {
            $query = app(AppartementSearchService::class)->buildQuery($appartement);

            Search::updateOrCreate(
                ['appartement_uuid' => $appartement->uuid],
                [
                    'uuid' => Str::uuid(),
                    'property_uuid' => $appartement->property_uuid,
                    'appartement_uuid' => $appartement->uuid,
                    'query' => $query,
                ]
            );

            $bar->advance();
        }

        $bar->finish();

        $this->info("\nRe-indexage terminé pour {$appartements->count()} appartements.");
    }
}
