<?php

namespace App\Models;

use App\Models\Appartement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tarification extends Model
{
    use HasFactory;
    protected $fillable = [
        'uuid',
        'code',
        'appart_uuid',
        'sejour',
        'nbr_of_sejour',
        'price',
        'updated_by',
        'created_by',
        'deleted_by',
        'etat',
    ];
    

    public function appartement()
    {
        return $this->belongsTo(Appartement::class, 'appart_uuid', 'uuid');
    }
}
