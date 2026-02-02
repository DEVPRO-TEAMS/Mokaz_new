<?php

namespace App\Models;

use App\Models\Visit;
use App\Models\Appartement;
use App\Models\PageViewHistorique;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PageView extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'visit_uuid',
        'url',
        'page_type',
        'appartement_uuid',
    ];

    public function historiques(): HasMany
    {
        return $this->hasMany(PageViewHistorique::class, 'pageview_uuid', 'uuid');
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class, 'visit_uuid', 'uuid');
    }
    public function appartement(): BelongsTo
    {
        return $this->belongsTo(Appartement::class, 'appartement_uuid', 'uuid');
    }

}
