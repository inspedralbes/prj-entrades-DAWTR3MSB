<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $event = \App\Models\Esdeveniment::create([
            'nom' => 'Concert de Jazz a Gràcia',
            'data_hora' => now()->addDays(10),
            'recinte' => 'Auditori de Barcelona',
            'descripcio' => 'Una nit inoblidable amb els millors músics de jazz.',
            'aforament' => 50
        ]);

        for ($i = 1; $i <= 50; $i++) {
            \App\Models\Seient::create([
                'esdeveniment_id' => $event->id,
                'fila' => ceil($i / 10),
                'numero' => ($i - 1) % 10 + 1,
                'estat' => 'disponible',
                'preu' => 25.00
            ]);
        }
    }
}
