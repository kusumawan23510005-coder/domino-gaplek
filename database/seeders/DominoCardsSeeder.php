<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DominoCardsSeeder extends Seeder
{
    public function run(): void
    {
        $cards = [];

        for ($i = 0; $i <= 6; $i++) {
            for ($j = $i; $j <= 6; $j++) {
                $cards[] = [
                    'left_value' => $i,
                    'right_value' => $j,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('domino_cards')->insert($cards);
    }
}
