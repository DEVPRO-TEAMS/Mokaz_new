<?php

namespace App\Observers;

use App\Models\Variable;
use App\Services\Search\AppartementSearchService;
use App\Models\Appartement;
use App\Models\Property;

class VariableObserver
{
    public function saved(Variable $variable)
    {
        // Appartements
        Appartement::where('type_uuid', $variable->uuid)
            ->each(fn ($app) =>
                app(AppartementSearchService::class)->reindexAppartement($app)
            );

        // Propriétés
        Property::where('type_uuid', $variable->uuid)
            ->orWhere('category_uuid', $variable->uuid)
            ->each(function ($property) {
                foreach ($property->apartements as $app) {
                    app(AppartementSearchService::class)->reindexAppartement($app);
                }
            });
    }

    public function deleted(Variable $variable)
    {
        // Appartements
        Appartement::where('type_uuid', $variable->uuid)
            ->each(fn ($app) =>
                app(AppartementSearchService::class)->reindexAppartement($app)
            );

        // Propriétés
        Property::where('type_uuid', $variable->uuid)
            ->orWhere('category_uuid', $variable->uuid)
            ->each(function ($property) {
                foreach ($property->apartements as $app) {
                    app(AppartementSearchService::class)->reindexAppartement($app);
                }
            });
    }
}

