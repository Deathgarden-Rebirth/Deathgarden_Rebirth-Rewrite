<?php

namespace App\Http\Controllers\Web\Admin\Tools;

use App\Enums\Auth\Permissions;
use App\Enums\Game\ExperienceEventType;
use App\Http\Requests\Api\Admin\Tools\SaveCurrencyConfiguration;
use App\Http\Requests\Api\Admin\Tools\SaveLauncherMessageRequest;
use App\Http\Requests\Api\Admin\Tools\SaveMatchConfigurationRequest;
use App\Http\Requests\Api\Admin\Tools\SaveVersioningRequest;
use App\Models\Admin\CurrencyMultipliers;
use App\Models\Admin\ExperienceMultipliers;
use App\Models\Admin\LauncherMessage;
use App\Models\Admin\Versioning\CurrentCatalogVersion;
use App\Models\Admin\Versioning\CurrentContentVersion;
use App\Models\Admin\Versioning\CurrentGameVersion;
use App\Models\Admin\Versioning\LauncherVersion;
use Session;

class MatchConfigurationController extends AdminToolController
{
    protected static string $name = 'Match Configuration';

    protected static string $description = 'Manage Events & Game Modes';
    protected static string $iconComponent = 'icons.globe';

    protected static Permissions $neededPermission = Permissions::FILE_UPLOAD;

    public function index()
    {
        return view('admin.tools.match-configuration');
    }

    public function saveExperience(SaveMatchConfigurationRequest $request)
    {
        $experienceMultipliers = ExperienceMultipliers::get();

        try {
            $experienceMultipliers->setEventTypeMultiplier(ExperienceEventType::ConstructDefeats, $request->constructDefeatsMultiplier);
            $experienceMultipliers->setEventTypeMultiplier(ExperienceEventType::Downing, $request->downingMultiplier);
            $experienceMultipliers->setEventTypeMultiplier(ExperienceEventType::Drones, $request->dronesMultiplier);
            $experienceMultipliers->setEventTypeMultiplier(ExperienceEventType::Execution, $request->executionMultiplier);
            $experienceMultipliers->setEventTypeMultiplier(ExperienceEventType::GardenFinale, $request->gardenFinaleMultiplier);
            $experienceMultipliers->setEventTypeMultiplier(ExperienceEventType::Hacking, $request->hackingMultiplier);
            $experienceMultipliers->setEventTypeMultiplier(ExperienceEventType::HunterClose, $request->hunterCloseMultiplier);
            $experienceMultipliers->setEventTypeMultiplier(ExperienceEventType::Resources, $request->resourcesMultiplier);
            $experienceMultipliers->setEventTypeMultiplier(ExperienceEventType::TeamActions, $request->teamActionsMultiplier);
            $experienceMultipliers->save();

            Session::flash('alert-success', 'Configuration saved successfully.');
        } catch (\Exception $e) {
            Session::flash('alert-error', 'Configuration could not be saved, something went wrong: ' . $e->getMessage());
        }

        return back();
    }

    public function saveCurrency(SaveCurrencyConfiguration $request)
    {
        $currencyMultiplier = CurrencyMultipliers::get();

        try {
            $currencyMultiplier->currencyA = $request->currencyAMultiplier;
            $currencyMultiplier->currencyB = $request->currencyBMultiplier;
            $currencyMultiplier->currencyC = $request->currencyCMultiplier;
            $currencyMultiplier->save();

            Session::flash('alert-success', 'Configuration saved successfully.');
        } catch (\Exception $e) {
            Session::flash('alert-error', 'Configuration could not be saved, something went wrong: ' . $e->getMessage());
        }

        return back();
    }

}