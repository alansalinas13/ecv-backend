<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $cities = [
            ['name' => 'Asunción'],
            ['name' => 'Ciudad del Este'],
            ['name' => 'Encarnación'],
            ['name' => 'Luque'],
            ['name' => 'San Lorenzo'],
            ['name' => 'Lambaré'],
            ['name' => 'Fernando de la Mora'],
            ['name' => 'Capiatá'],
            ['name' => 'Limpio'],
            ['name' => 'Ñemby'],
            ['name' => 'Areguá'],
            ['name' => 'Villa Elisa'],
            ['name' => 'Itauguá'],
            ['name' => 'Caacupé'],
            ['name' => 'Coronel Oviedo'],
            ['name' => 'Villarrica'],
            ['name' => 'Pedro Juan Caballero'],
            ['name' => 'Concepción'],
            ['name' => 'Paraguarí'],
            ['name' => 'Pilar'],
            ['name' => 'Caazapá'],
            ['name' => 'Salto del Guairá'],
            ['name' => 'Curuguaty'],
            ['name' => 'Filadelfia'],
            ['name' => 'Fuerte Olimpo'],
            ['name' => 'San Pedro del Ycuamandiyú'],
        ];

        foreach ($cities as $city) {
            DB::table('cities')->updateOrInsert(
                ['name' => $city['name']],
                $city
            );
        }
    }
}
