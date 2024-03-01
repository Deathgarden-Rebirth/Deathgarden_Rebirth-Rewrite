<?php

namespace App\Http\Controllers\Api\Player;

use App\Helper\Uuid\UuidHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Player\GetChallengeProgressionBatchRequest;
use App\Http\Responses\Api\Player\Challenges\ChallengeProgressionBatchResponse;
use App\Http\Responses\Api\Player\Challenges\ChallengeProgressionEntry;
use App\Models\Game\Challenge;
use App\Models\Game\PickedChallenge;
use App\Models\User\User;
use Illuminate\Support\Collection;
use Ramsey\Uuid\Uuid;

class ChallengeController extends Controller
{
    /**
     * The "extensions/challenges/getChallenges" is just used for getting the current Daily/Weekly/Event challenges.
     * So we just return an empty array for now, so that we don't have any of them.
     *
     * @return false|string
     */
    public function getChallenges()
    {
        return json_encode(['challenges' => []]);
    }

    public function getProgressionBatch(GetChallengeProgressionBatchRequest $request)
    {
        $user = User::findOrFail($request->userId);
        $playerData = $user->playerData();

        $challengeIdsToCheck = UuidHelper::convertFromHexToUuidCollecton($request->challengeIds, true);

        /** @var Challenge[]|Collection $challengesToCheck */
        $challengesToCheck = Challenge::findMany($challengeIdsToCheck);
        /** @var PickedChallenge[]|Collection $pickedChallengesToCheck */
        $pickedChallengesToCheck = PickedChallenge::findMany($challengeIdsToCheck);

        $response = new ChallengeProgressionBatchResponse();

        foreach ($challengesToCheck as $challenge) {
            $progress = $challenge->getProgressForPlayer($playerData->id);

            $newEntry = new ChallengeProgressionEntry(
                Uuid::fromString($challenge->id)->getHex()->toString(),
                $progress >= $challenge->completion_value,
                $progress,
            );

            $response->progressionBatch[] = $newEntry;
        }

        foreach ($pickedChallengesToCheck as $challenge) {
            $response->progressionBatch[] = new ChallengeProgressionEntry(
                Uuid::fromString($challenge->id)->getHex()->toString(),
                $challenge->progress >= $challenge->completion_value,
                $challenge->progress,
            );
        }

        return json_encode($response);
    }
}
