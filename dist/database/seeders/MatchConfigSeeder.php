<?php

namespace Database\Seeders;

use App\Models\Game\Matchmaking\MatchConfiguration;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MatchConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('match_configurations')->delete();
        MatchConfiguration::Create([
            'name' => 'Curefew 1v5 - Slums',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_SLU_DownTown.MatchConfig_SLU_DownTown',
            'enabled' => false,
        ]);
        MatchConfiguration::Create([
            'name' => 'Harvest Your Exit - 1v5',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_Demo_HarvestYourExit_1v5.MatchConfig_Demo_HarvestYourExit_1v5',
            'enabled' => false,
        ]);
        MatchConfiguration::Create([
            'name' => 'Harvest Your Exit - 1v4',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_Demo_HarvestYourExit_1v5.MatchConfig_Demo_HarvestYourExit_1v5',
            'enabled' => false,
            'hunters' => 1,
            'runners' => 4,
            'weight' => 10,
        ]);
        MatchConfiguration::Create([
            'name' => 'Harvest Your Exit - 1v6',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_Demo_HarvestYourExit_1v5.MatchConfig_Demo_HarvestYourExit_1v5',
            'enabled' => false,
            'hunters' => 1,
            'runners' => 6,
            'weight' => 10,
        ]);
        MatchConfiguration::Create([
            'name' => 'New Arctic Fortress',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_ARC_Fortress.MatchConfig_ARC_Fortress',
            'enabled' => true,
            'weight' => 50,
        ]);
        MatchConfiguration::Create([
            'name' => 'Survival 1v5 - All Maps',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_Demo.MatchConfig_Demo',
            'enabled' => false,
        ]);
        MatchConfiguration::Create([
            'name' => 'Fire in the Sky - 1v5',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_ARC_BlastFurnace.MatchConfig_ARC_BlastFurnace',
            'enabled' => true,
            'weight' => 200,
        ]);
        MatchConfiguration::Create([
            'name' => 'Fire in the Sky - 2v10',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_ARC_BlastFurnace_2Hunters.MatchConfig_ARC_BlastFurnace_2Hunters',
            'enabled' => false,
            'hunters' => 2,
            'runners' => 10,
        ]);
        MatchConfiguration::Create([
            'name' => 'Desperate Expedition - 1v5',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_ARC_Expedition.MatchConfig_ARC_Expedition',
            'enabled' => false,
        ]);
        MatchConfiguration::Create([
            'name' => 'Desperate Expedition - 2v10',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_ARC_Expedition_2Hunters.MatchConfig_ARC_Expedition_2Hunters',
            'enabled' => false,
            'hunters' => 2,
            'runners' => 10,
        ]);
        MatchConfiguration::Create([
            'name' => 'Scrap Yard - 1v5',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_ARC_ScrapYard.MatchConfig_ARC_ScrapYard',
            'enabled' => false,
        ]);
        MatchConfiguration::Create([
            'name' => 'Scrap Yard - 2v10',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_ARC_ScrapYard_2Hunters.MatchConfig_ARC_ScrapYard_2Hunters',
            'enabled' => false,
            'hunters' => 2,
            'runners' => 10,
        ]);
        MatchConfiguration::Create([
            'name' => 'Caves All - 1v5',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_CAV_All.MatchConfig_CAV_All',
            'enabled' => false,
        ]);
        MatchConfiguration::Create([
            'name' => 'Caves All - 2v10',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_CAV_All_2Hunters.MatchConfig_CAV_All_2Hunters',
            'enabled' => false,
            'hunters' => 2,
            'runners' => 10,
        ]);
        MatchConfiguration::Create([
            'name' => 'Custom',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_Custom.MatchConfig_Custom',
            'enabled' => false,
        ]);
        MatchConfiguration::Create([
            'name' => 'Custom Match',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_CustomMatch.MatchConfig_CustomMatch',
            'enabled' => false,
        ]);
        MatchConfiguration::Create([
            'name' => 'Survival 2v10 - 4 Needles',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_Demo_2v10_4Needles.MatchConfig_Demo_2v10_4Needles',
            'enabled' => false,
            'hunters' => 2,
            'runners' => 10,
        ]);
        MatchConfiguration::Create([
            'name' => 'Survival 2v10 - 5 Needles',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_Demo_2v10_5Needles.MatchConfig_Demo_2v10_5Needles',
            'enabled' => false,
            'hunters' => 2,
            'runners' => 10,
        ]);
        MatchConfiguration::Create([
            'name' => 'Survival 2v8 - 4 Needles',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_Demo_2v8_4Needles.MatchConfig_Demo_2v8_4Needles',
            'enabled' => false,
            'hunters' => 2,
            'runners' => 8,
        ]);
        MatchConfiguration::Create([
            'name' => 'Survival 2v8 - 5 Needles',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_Demo_2v8_5Needles.MatchConfig_Demo_2v8_5Needles',
            'enabled' => false,
            'hunters' => 2,
            'runners' => 8,
        ]);
        MatchConfiguration::Create([
            'name' => 'Harvest Your Exit - 2v10',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_Demo_HarvestYourExit.MatchConfig_Demo_HarvestYourExit',
            'enabled' => false,
            'hunters' => 2,
            'runners' => 10,
        ]);
        MatchConfiguration::Create([
            'name' => 'Barren City - 1v5',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_DES_City.MatchConfig_DES_City',
            'enabled' => false,
        ]);
        MatchConfiguration::Create([
            'name' => 'Barren City - 2v10',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_DES_City_2Hunters.MatchConfig_DES_City_2Hunters',
            'enabled' => false,
            'hunters' => 2,
            'runners' => 10,
        ]);
        MatchConfiguration::Create([
            'name' => 'Legions Rest - 1v5',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_DES_Fortress.MatchConfig_DES_Fortress',
            'enabled' => true,
            'weight' => 100,
        ]);
        MatchConfiguration::Create([
            'name' => 'Legions Rest - 2v10',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_DES_Fortress_2Hunters.MatchConfig_DES_Fortress_2Hunters',
            'enabled' => false,
            'hunters' => 2,
            'runners' => 10,
        ]);
        MatchConfiguration::Create([
            'name' => 'Gold Rush - 1v5',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_DES_GoldRush.MatchConfig_DES_GoldRush',
            'enabled' => false,
            'weight' => 100,
        ]);
        MatchConfiguration::Create([
            'name' => 'Gold Rush 2v10 - 5 Needles',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_DES_GoldRush_2v10_5Needles.MatchConfig_DES_GoldRush_2v10_5Needles',
            'enabled' => false,
            'hunters' => 2,
            'runners' => 10,
        ]);
        MatchConfiguration::Create([
            'name' => 'Dust & Blood - 1v5',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_DES_Mayan.MatchConfig_DES_Mayan',
            'enabled' => true,
            'weight' => 100,
        ]);
        MatchConfiguration::Create([
            'name' => 'Dust & Blood 2v10 - 5 Needles',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_DES_Mayan_2v10_5Needles.MatchConfig_DES_Mayan_2v10_5Needles',
            'enabled' => false,
            'hunters' => 2,
            'runners' => 10,
        ]);
        MatchConfiguration::Create([
            'name' => 'Blowout - 1v5',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_DES_Oilfield.MatchConfig_DES_Oilfield',
            'enabled' => true,
            'weight' => 100,
        ]);
        MatchConfiguration::Create([
            'name' => 'Blowout - 2v10',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_DES_Oilfield_2Hunters.MatchConfig_DES_Oilfield_2Hunters',
            'enabled' => false,
            'hunters' => 2,
            'runners' => 10,
        ]);
        MatchConfiguration::Create([
            'name' => 'Forest Citadel - 1v5',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_JUN_Fortress.MatchConfig_JUN_Fortress',
            'enabled' => true,
            'weight' => 50,
        ]);
        MatchConfiguration::Create([
            'name' => 'Forest Citadel - 2v10',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_JUN_Fortress_2Hunters.MatchConfig_JUN_Fortress_2Hunters',
            'enabled' => false,
            'hunters' => 2,
            'runners' => 10,
        ]);
        MatchConfiguration::Create([
            'name' => 'New Maps',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_NewMaps.MatchConfig_NewMaps',
            'enabled' => false,
        ]);
        MatchConfiguration::Create([
            'name' => 'All New Arctic Maps',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_PRM_Special.MatchConfig_PRM_Special',
            'enabled' => false,
        ]);
        MatchConfiguration::Create([
            'name' => 'First Strike - 1v5',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_RUI_All.MatchConfig_RUI_All',
            'enabled' => true,
            'weight' => 100,
        ]);
        MatchConfiguration::Create([
            'name' => 'First Strike - 2v10',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_RUI_All_2Hunters.MatchConfig_RUI_All_2Hunters',
            'enabled' => false,
            'hunters' => 2,
            'runners' => 10,
        ]);
        MatchConfiguration::Create([
            'name' => 'Tombstone - 1v5',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_WA_Cemetery.MatchConfig_WA_Cemetery',
            'enabled' => true,
            'weight' => 140,
        ]);
        MatchConfiguration::Create([
            'name' => 'Tombstone - 2v10',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_WA_Cemetery_2Hunters.MatchConfig_WA_Cemetery_2Hunters',
            'enabled' => false,
            'hunters' => 2,
            'runners' => 10,
        ]);
        MatchConfiguration::Create([
            'name' => 'Salt Creek - 1v5',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_WA_Rivers.MatchConfig_WA_Rivers',
            'enabled' => true,
            'weight' => 200,
        ]);
        MatchConfiguration::Create([
            'name' => 'Salt Creek - 2v10',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_WA_Rivers_2Hunters.MatchConfig_WA_Rivers_2Hunters',
            'enabled' => false,
            'hunters' => 2,
            'runners' => 10,
        ]);
        MatchConfiguration::Create([
            'name' => 'Harvest Your Exit - 1v1 (DEV)',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_Demo_HarvestYourExit_1v5.MatchConfig_Demo_HarvestYourExit_1v5',
            'hunters' => 1,
            'runners' => 1,
            'enabled' => false,
        ]);
        MatchConfiguration::Create([
            'name' => 'Harvest Your Exit - 1v6 (EXPERIMENTAL)',
            'asset_path' => '/Game/Configuration/MatchConfig/MatchConfig_Demo_HarvestYourExit_1v5.MatchConfig_Demo_HarvestYourExit_1v5',
            'hunters' => 1,
            'runners' => 6,
            'enabled' => false,
        ]);
    }
}
