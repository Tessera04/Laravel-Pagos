<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrenciesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            'USD',
            'EUR',
            'ARS',
        ];

        foreach($currencies as $currency){
            Currency::class([
                'iso' => $currency,
            ]);
        }
    }
}
