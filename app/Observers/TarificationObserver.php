<?php

namespace App\Observers;

use App\Models\Tarification;
use App\Services\Search\AppartementSearchService;

class TarificationObserver
{
    public function saved(Tarification $tarif)
    {
        if ($tarif->appartement) {
            app(AppartementSearchService::class)
                ->reindexAppartement($tarif->appartement);
        }
    }

    public function deleted(Tarification $tarif)
    {
        if ($tarif->appartement) {
            app(AppartementSearchService::class)
                ->reindexAppartement($tarif->appartement);
        }
    }
}
