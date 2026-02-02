<?php

namespace App\Models;

use App\Models\PageView;
use App\Models\VisitHistorique;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Visit extends Model
{
    use HasFactory;
    protected $fillable = [
        'uuid',
        'ip_address',
        'user_agent',
        'started_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
    ];

    public function historiques()
    {
        return $this->hasMany(VisitHistorique::class, 'visit_uuid', 'uuid');
    }

    public function pageViews()
    {
        return $this->hasMany(PageView::class, 'visit_uuid', 'uuid');
    }

    // Nouvelle méthode pour identifier un visiteur unique
    public function getVisitorKeyAttribute(): string
    {
        return md5($this->ip_address . '-' . $this->user_agent);
    }
}
