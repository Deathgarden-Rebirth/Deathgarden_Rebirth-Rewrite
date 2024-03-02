<?php

namespace Database\Seeders;

use App\Models\Game\MatchConfiguration;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MatchConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MatchConfiguration::truncate();
        MatchConfiguration::Create([
            'name' => 'Slums',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_SLU_DownTown.MatchConfig_SLU_DownTown',
            'enabled' => false,
        ]);
        MatchConfiguration::Create([
            'name' => 'New Arctic Fortress',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_ARC_Fortress.MatchConfig_ARC_Fortress',
        ]);
    }
}
