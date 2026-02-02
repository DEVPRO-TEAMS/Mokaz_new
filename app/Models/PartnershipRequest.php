<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnershipRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'company',
        'property_type',
        'activity_zone',
        'experience',
        'portfolio_size',
        'website',
        'message',
        'accepts_newsletter',
        'etat'
    ];

    protected $casts = [
        'accepts_newsletter' => 'boolean'
    ];

    public static function getPropertyTypes()
    {
        return [
            'residential' => 'Résidentiel',
            'commercial' => 'Commercial',
            'industrial' => 'Industriel',
            'land' => 'Terrains',
            'mixed' => 'Mixte'
        ];
    }

    public static function getExperienceLevels()
    {
        return [
            '0-2' => '0-2 ans',
            '3-5' => '3-5 ans',
            '6-10' => '6-10 ans',
            '10+' => 'Plus de 10 ans'
        ];
    }

    public static function getPortfolioSizes()
    {
        return [
            '1-10' => '1-10 biens',
            '11-50' => '11-50 biens',
            '51-100' => '51-100 biens',
            '100+' => 'Plus de 100 biens'
        ];
    }
}
