<?php

namespace App\Observers;

use App\Models\Property;
use App\Services\Search\AppartementSearchService;

class PropertyObserver
{
    public function saved(Property $property)
    {
        foreach ($property->apartements as $appartement) {
            app(AppartementSearchService::class)
                ->reindexAppartement($appartement);
        }
    }

    public function deleted(Property $property)
    {
        foreach ($property->apartements as $appartement) {
            app(AppartementSearchService::class)
                ->reindexAppartement($appartement);
        }
    }
}
