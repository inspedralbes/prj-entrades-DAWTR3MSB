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
        $esdeveniments = [
            [
                'nom' => 'Concert de Jazz a Gràcia',
                'data_hora' => now()->addDays(2),
                'recinte' => 'Auditori de Barcelona',
                'descripcio' => 'La millor nit de jazz del 2026. Grans clàssics i noves veus.',
                'aforament' => 50,
                'preu' => 25.00,
                'imatge_url' => 'https://images.unsplash.com/photo-1511192303578-4a7b974a4286?auto=format&fit=crop&q=80&w=800'
            ],
            [
                'nom' => 'Interstellar - Reestrena (IMAX)',
                'data_hora' => now()->addDays(5),
                'recinte' => 'Filmoteca de Catalunya',
                'descripcio' => 'Viu l\'experiència de Christopher Nolan en pantalla gran.',
                'aforament' => 50,
                'preu' => 12.00,
                'imatge_url' => 'https://images.unsplash.com/photo-1446776811953-b23d57bd21aa?auto=format&fit=crop&q=80&w=800'
            ],
            [
                'nom' => 'The Weeknd - After Hours Tour',
                'data_hora' => now()->addDays(12),
                'recinte' => 'Palau Sant Jordi',
                'descripcio' => 'L\'artista més escoltat del món arriba a Barcelona.',
                'aforament' => 50,
                'preu' => 85.00,
                'imatge_url' => 'https://images.unsplash.com/photo-1540039155733-5bb30b53aa14?auto=format&fit=crop&q=80&w=800'
            ],
            [
                'nom' => 'Hamlet (Teatre Nacional)',
                'data_hora' => now()->addDays(15),
                'recinte' => 'TNC Sala Gran',
                'descripcio' => 'L\'obra mestra de Shakespeare en una producció moderna i atrevida.',
                'aforament' => 50,
                'preu' => 35.00,
                'imatge_url' => 'https://images.unsplash.com/photo-1507676184212-d03ab07a01bf?auto=format&fit=crop&q=80&w=800'
            ],
            [
                'nom' => 'Mar i Cel (Musical)',
                'data_hora' => now()->addDays(20),
                'recinte' => 'Teatre Victòria',
                'descripcio' => 'El retorn de Dagoll Dagom amb el seu vaixell pirata més famós.',
                'aforament' => 50,
                'preu' => 45.00,
                'imatge_url' => 'https://images.unsplash.com/photo-1544033527-b192daee1f5b?auto=format&fit=crop&q=80&w=800'
            ],
            [
                'nom' => 'Rauw Alejandro - Saturno',
                'data_hora' => now()->addDays(30),
                'recinte' => 'Estadi Olímpic',
                'descripcio' => 'El millor reggaeton del moment amb una posada en escena galàctica.',
                'aforament' => 50,
                'preu' => 60.00,
                'imatge_url' => 'https://images.unsplash.com/photo-1493225255756-d9584f8606e9?auto=format&fit=crop&q=80&w=800'
            ]
        ];

        foreach ($esdeveniments as $data) {
            $event = \App\Models\Esdeveniment::create([
                'nom' => $data['nom'],
                'data_hora' => $data['data_hora'],
                'recinte' => $data['recinte'],
                'imatge_url' => $data['imatge_url'],
                'descripcio' => $data['descripcio'],
                'aforament' => $data['aforament']
            ]);

            for ($i = 1; $i <= $data['aforament']; $i++) {
                \App\Models\Seient::create([
                    'esdeveniment_id' => $event->id,
                    'fila' => ceil($i / 10),
                    'numero' => ($i - 1) % 10 + 1,
                    'estat' => 'disponible',
                    'preu' => $data['preu']
                ]);
            }
        }
    }
}
