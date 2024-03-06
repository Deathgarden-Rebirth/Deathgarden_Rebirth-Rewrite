<?php

namespace App\Http\Controllers\Api\Player;

use App\Helper\Uuid\UuidHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Player\ExecuteChallengeProgressionBatchRequest;
use App\Http\Requests\Api\Player\GetChallengeProgressionBatchRequest;
use App\Http\Responses\Api\Player\Challenges\ChallengeProgressionBatchResponse;
use App\Http\Responses\Api\Player\Challenges\ChallengeProgressionEntry;
use App\Models\Game\Challenge;
use App\Models\Game\Matchmaking\Game;
use App\Models\Game\PickedChallenge;
use App\Models\User\User;
use Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Collection;
use Illuminate\Validation\UnauthorizedException;
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

    public function executeChallengeProgressionBatch(ExecuteChallengeProgressionBatchRequest $request)
    {
        $user = Auth::user();
        /** @var Game $foundGame */
        $foundGame = $user->games()->orderBy('created_at', 'desc')->first();

        // Only allow the saving of challenge progress if the request comes from the same user as from the
        // game the request is coming from.
        if($user != $foundGame->creator)
            throw new AuthorizationException('Not allowed to save progress');

        $progressUser = User::find($request->userId);
        $processedChallenges = [];

        foreach ($request->operations as $operation) {
            $challengeId = Uuid::fromString($operation['challengeId'])->toString();

            // Skip if we already processed this challenge because the completed ones are always first in the array.
            if(in_array($challengeId, $processedChallenges))
                continue;

            if($operation['operationName'] === 'complete')
                $this->setChallengeAsCompleted($challengeId, $progressUser);
            else
                $this->addProgressToChallenge($challengeId, round($operation['operationData']['value']), $progressUser);
            $processedChallenges[] = $challengeId;
        }
    }

    protected function addProgressToChallenge(string $challengeId, int $progressToAdd, User $user): void
    {
        /** @var Challenge|null $foundChallenge */
        $foundChallenge = $user->playerData()->challenges()->find($challengeId);

        if($foundChallenge !== null) {
            $foundChallenge->playerData()->updateExistingPivot($user->playerData()->id, [
                'progress' => $foundChallenge->pivot->progress + $progressToAdd,
            ]);
            return;
        }

        $foundChallenge = PickedChallenge::find($challengeId);
        if($foundChallenge === null)
            return;

        $foundChallenge->progress += $progressToAdd;
        $foundChallenge->save();
    }

    protected function setChallengeAsCompleted(string $challengeId, User $user)
    {
        /** @var Challenge|null $foundChallenge */
        $foundChallenge = $user->playerData()->challenges()->find($challengeId);

        if($foundChallenge !== null) {
            $foundChallenge->playerData()->updateExistingPivot($user->playerData()->id, [
                'progress' => $foundChallenge->completion_value,
            ]);
            $foundChallenge->save();
            return;
        }

        $foundChallenge = PickedChallenge::find($challengeId);
        if($foundChallenge === null)
            return;

        $foundChallenge->progress = $foundChallenge->completion_value;
        $foundChallenge->save();
    }
}
