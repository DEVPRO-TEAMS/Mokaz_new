<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Search extends Model
{
    use HasFactory;
    protected $table = 'searches';
    protected $fillable = [
        'uuid',
        'property_code',
        'property_uuid',
        'appartement_code',
        'appartement_uuid',
        'query',
    ];

    public function appartement()
    {
        return $this->belongsTo(Appartement::class, 'appartement_uuid', 'uuid');
    }

    public function property()
    {
        return $this->appartement->property();
    }
}
