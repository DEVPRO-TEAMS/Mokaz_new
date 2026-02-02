<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class country extends Model
{
    use HasFactory;
    // le fillable
    protected $fillable = [
        'code',
        'label',
        'etat',
    ];
}
