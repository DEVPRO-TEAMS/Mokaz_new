<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitHistorique extends Model
{
    use HasFactory;
    protected $fillable = [
        'uuid',
        'visit_uuid',
        'source',
        'referrer',
        'coordinates',
        'country',
        'city',
        'started_at',
        'last_activity_at',
        'ended_at',
        'duration',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function visit()
    {
        return $this->belongsTo(Visit::class, 'visit_uuid', 'uuid');
    }

    // Scope pour filtrer les sessions valides
    public function scopeValidSessions($query)
    {
        return $query->whereNotNull('ended_at')
                     ->where('duration', '<=', 7200); // Max 2 heures
    }

    // Scope pour les sessions en cours
    public function scopeActive($query)
    {
        return $query->whereNull('ended_at');
    }
}
