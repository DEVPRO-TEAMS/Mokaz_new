<?php

namespace App\Models;

use App\Models\Appartement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppartDoc extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'appartement_uuid',
        'doc_name',
        'doc_url',
        'etat',
    ];
    
    public function appartement()
    {
        return $this->belongsTo(Appartement::class, 'appartement_uuid', 'uuid');
    }
}
