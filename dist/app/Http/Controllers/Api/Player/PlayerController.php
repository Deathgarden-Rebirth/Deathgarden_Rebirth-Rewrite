<?php

namespace App\Http\Controllers\Api\Player;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Player\InitOrGetGroupsRequest;
use App\Http\Responses\Api\Player\GetBanStatusResponse;
use App\Http\Responses\Api\Player\InitOrGetGroupsResponse;
use App\Http\Responses\Api\Player\PlayerData;
use App\Http\Responses\Api\Player\SplinteredState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlayerController extends Controller
{
    const PROGRESSIONS_GROUPS = [
        'RunnerProgression',
        'HunterProgression',
        'PlayerProgression',
    ];

    public function getBanStatus()
    {
        $user = Auth::user();

        if ($user->ban === null)
            return json_encode(new GetBanStatusResponse(false));

        return json_encode(new GetBanStatusResponse(true, $user->ban));
    }

    public function initOrGetGroups(InitOrGetGroupsRequest $request)
    {
        $response = new InitOrGetGroupsResponse();

        if (!$request->skipProgressionGroups) {
            $groups = [];
            foreach (static::PROGRESSIONS_GROUPS as $group) {
                $RunnerMetadataGroup = new SplinteredState();
                $RunnerMetadataGroup->ObjectId = $group;
                $RunnerMetadataGroup->Version = 2.11;
                $RunnerMetadataGroup->SchemaVersion = 2.11;

                // TODO: Make into model
                $data = new \stdClass();
                $data->Level = 1;
                $data->CurrentExperience = 10;
                $data->ExperienceToReach = 30;
                $RunnerMetadataGroup->Data = $data;

                $groups[] = $RunnerMetadataGroup;
            }
            $response->ProgressionGroups = $groups;
        }

        if (!$request->skipMetadataGroups) {
            $user = Auth::user();

            $runnerMetadata = new SplinteredState();
            $runnerMetadata->ObjectId = 'RunnerMetadata';
            $runnerMetadata->Version = 2.11;
            $runnerMetadata->SchemaVersion = 2.11;
            $runnerMetadata->setDataFromCharacterData($user->playerData()->lastRunnerCharacterData());

            $hunterMetadata = new SplinteredState();
            $hunterMetadata->ObjectId = 'RunnerMetadata';
            $hunterMetadata->Version = 2.11;
            $hunterMetadata->SchemaVersion = 2.11;
            $hunterMetadata->setDataFromCharacterData($user->playerData()->lastRunnerCharacterData());

            $playerMetadata = new SplinteredState();
            $playerMetadata->ObjectId = 'RunnerMetadata';
            $playerMetadata->Version = 2.11;
            $playerMetadata->SchemaVersion = 2.11;
            $playerMetadata->Data = new PlayerData($user->playerData());

            $response->MetadataGroups = [$runnerMetadata, $hunterMetadata, $playerMetadata];
        }



        return json_encode($response);
    }
}
