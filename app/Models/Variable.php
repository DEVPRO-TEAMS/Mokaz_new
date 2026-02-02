<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Variable extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'code',
        'libelle',
        'description',
        'type',
        'category_uuid',
        'category',
        'etat',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }
}
