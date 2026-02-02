<?php

namespace App\Observers;

use App\Models\Appartement;
use App\Models\Search;
use App\Services\Search\AppartementSearchService;
use Illuminate\Support\Str;

class AppartementObserver
{
    public function saved(Appartement $appartement)
    {
        // $appartement->load([
        //     'property.ville',
        //     'property.pays',
        //     'property.type',
        //     'property.category',
        //     'tarifications'
        // ]);

        // $query = app(AppartementSearchService::class)
        //     ->buildQuery($appartement);

        // Search::updateOrCreate(
        //     [
        //         'appartement_uuid' => $appartement->uuid,
        //     ],
        //     [
        //         'uuid' => Str::uuid(),
        //         'property_uuid' => $appartement->property_uuid,
        //         'property_code' => $appartement->property->code,
        //         'appartement_uuid' => $appartement->uuid,
        //         'appartement_code' => $appartement->code,
        //         'query' => $query,
        //     ]
        // );
        app(AppartementSearchService::class)
        ->reindexAppartement($appartement);
    }

    public function deleted(Appartement $appartement)
    {
        Search::where('appartement_uuid', $appartement->uuid)->delete();
    }
}

