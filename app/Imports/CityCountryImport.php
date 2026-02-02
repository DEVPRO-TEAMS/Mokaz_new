<?php

namespace App\Imports;

use App\Models\City;
// use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Country;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Str;

class CityCountryImport implements ToCollection
// class CityCountryImport implements ToModel
{
    // /**
    // * @param array $row
    // *
    // * @return \Illuminate\Database\Eloquent\Model|null
    // */
    // public function model(array $row)
    // {
    //     return new City([
    //         //
    //     ]);
    // }

    public function collection(Collection $rows)
    {
        // Ignorer l'en-tête
        $rows->shift();

        $countries = [];

        foreach ($rows as $row) {
            $cityName = trim($row[0]);
            $countryName = trim($row[1]);
            $iso3 = strtoupper(trim($row[2]));

            if (!$countryName || !$iso3 || !$cityName) {
                continue;
            }

            // Génère un code unique pour le pays (par exemple : CIV)
            $countryCode = $iso3;

            // Si le pays n'existe pas encore, on l'ajoute à la table
            if (!isset($countries[$countryCode])) {
                $country = Country::firstOrCreate(
                    ['code' => $countryCode],
                    ['label' => $countryName]
                );

                $countries[$countryCode] = $country->id;
            }

            // Code de la ville : city-ISO3
            $cityCode = Str::slug($cityName) . '-' . $countryCode;

            City::updateOrCreate(
                ['code' => $cityCode],
                [
                    'label' => $cityName,
                    'country_code' => $countryCode
                ]
            );
        }
    }
}
