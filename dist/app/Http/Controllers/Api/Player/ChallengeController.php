<?php

namespace App\Http\Controllers\Api\Player;

use App\Enums\Game\RewardType;
use App\Helper\Uuid\UuidHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Player\ExecuteChallengeProgressionBatchRequest;
use App\Http\Requests\Api\Player\GetChallengeProgressionBatchRequest;
use App\Http\Requests\Api\Player\GetChallengesRequest;
use App\Http\Responses\Api\General\Reward;
use App\Http\Responses\Api\Player\Challenges\ChallengeProgressionBatchResponse;
use App\Http\Responses\Api\Player\Challenges\ChallengeProgressionEntry;
use App\Http\Responses\Api\Player\Challenges\GetChallengesEntry;
use App\Models\Game\Challenge;
use App\Models\Game\Matchmaking\Game;
use App\Models\Game\PickedChallenge;
use App\Models\Game\TimedChallenge;
use App\Models\User\PlayerData;
use App\Models\User\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
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
    public function getChallenges(GetChallengesRequest $request)
    {
        $user = $request->user;
        $timedChallenges = TimedChallenge::where('start_time', '<', Carbon::now())
            ->where('end_time', '>', Carbon::now())
            ->where('type', $request->type)
            ->get();

        $challenges = [];
        $timedChallenges->each(function (TimedChallenge $challenge) use (&$challenges, $user) {
            $challenges[] = new GetChallengesEntry(
                $challenge->id,
                $challenge->start_time,
                $challenge->end_time,
                $challenge->type,
                $challenge->completion_value,
                $challenge->faction,
                $challenge->blueprint_path,
                $challenge->rewards,
                $challenge->hasPlayerClaimed($user->playerData()->id),
            );
        });

        return Response::json(['challenges' => $challenges]);
    }

    public function getProgressionBatch(GetChallengeProgressionBatchRequest $request)
    {
        $user = User::findOrFail($request->userId);
        $playerData = $user->playerData();

        $timedChallengeIds = [];

        foreach ($request->challengeIds as $index => $id) {
            if(Str::startsWith($id, GetChallengesEntry::ID_PREFIX)) {
                $timedChallengeIds[] = Str::remove(GetChallengesEntry::ID_PREFIX, $id);
                unset($request->challengeIds[$index]);
            }
        }

        $challengeIdsToCheck = UuidHelper::convertFromHexToUuidCollecton($request->challengeIds, true);

        /** @var Challenge[]|Collection $challengesToCheck */
        $challengesToCheck = Challenge::findMany($challengeIdsToCheck);
        /** @var PickedChallenge[]|Collection $pickedChallengesToCheck */
        $pickedChallengesToCheck = PickedChallenge::findMany($challengeIdsToCheck);
        /** @var TimedChallenge[]|Collection $timedChallengesToCheck */
        $timedChallengesToCheck = TimedChallenge::findMany($timedChallengeIds);

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

        foreach ($timedChallengesToCheck as $challenge) {
            $progress = $challenge->getProgressForPlayer($playerData->id);

            $entry = new ChallengeProgressionEntry(
                GetChallengesEntry::ID_PREFIX . $challenge->id,
                $challenge->progress >= $challenge->completion_value,
                $progress,
            );

            $entry->rewardsClaimed = $challenge->rewards;

            $response->progressionBatch[] = $entry;
        }

        foreach ($pickedChallengesToCheck as $challenge) {
            $response->progressionBatch[] = new ChallengeProgressionEntry(
                Uuid::fromString($challenge->id)->getHex()->toString(),
                $challenge->progress >= $challenge->completion_value,
                $challenge->progress,
            );
        }

        foreach ($challengesToCheck as $challenge) {}

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
            $challengeId = $operation['challengeId'];

            if(!Str::startsWith($challengeId, GetChallengesEntry::ID_PREFIX)) {
                $challengeId = Uuid::fromString($operation['challengeId'])->toString();
            }

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

    protected function addProgressToChallenge(string $challengeId, int $newProgress, User $user): void
    {
        if(Str::startsWith($challengeId, GetChallengesEntry::ID_PREFIX)) {
            $foundChallenge = TimedChallenge::find(Str::remove(GetChallengesEntry::ID_PREFIX, $challengeId));
        }
        else {
            /** @var Challenge|null $foundChallenge */
            $foundChallenge = $user->playerData()->challenges()->find($challengeId);
        }

        if($foundChallenge !== null) {
            $foundChallenge->playerData()->updateExistingPivot($user->playerData()->id, [
                'progress' => $newProgress,
            ]);
            return;
        }

        /** @var PickedChallenge $foundChallenge */
        $foundChallenge = PickedChallenge::find($challengeId);
        if($foundChallenge === null)
            return;

        $newProgress = $newProgress >= MetadataController::reducePickedChallengeCompletionValue($foundChallenge->asset_path, $foundChallenge->completion_value) ? $foundChallenge->completion_value : $newProgress;

        $foundChallenge->progress = $newProgress;
        $foundChallenge->save();
    }

    protected function setChallengeAsCompleted(string $challengeId, User $user)
    {
        $isTimed = false;
        // If the Challenge id starts with the Prefix, handle it as a timed challenge.
        if(Str::startsWith($challengeId, GetChallengesEntry::ID_PREFIX)) {
            $foundChallenge = TimedChallenge::find(Str::remove(GetChallengesEntry::ID_PREFIX, $challengeId));
            $isTimed = true;
        }
        else {
            /** @var Challenge|null $foundChallenge */
            $foundChallenge = $user->playerData()->challenges()->find($challengeId);
        }

        if($foundChallenge !== null) {
            $pivotToEdit = [
                'progress' => $foundChallenge->completion_value,
            ];

            if($isTimed) {
                $playerData = $user->playerData();
                $hasClaimed = $foundChallenge->hasPlayerClaimed($playerData->id);

                if(!$hasClaimed) {
                    $rewardsToAdd = $foundChallenge->getRewards();

                    foreach($rewardsToAdd as $reward) {
                        $this->addReward($reward, $playerData);
                    }
                    $playerData->save();
                    $pivotToEdit['claimed'] = true;
                }
            }

            $foundChallenge->playerData()->updateExistingPivot($user->playerData()->id, $pivotToEdit);
            $foundChallenge->save();
            return;
        }

        $foundChallenge = PickedChallenge::find($challengeId);
        if($foundChallenge === null)
            return;

        $foundChallenge->progress = $foundChallenge->completion_value;
        $foundChallenge->save();
    }

    protected function addReward(Reward $reward, PlayerData &$playerData): void
    {
        if ($reward->type === RewardType::Currency) {
            switch ($reward->id) {
                case 'CurrencyA':
                    $playerData->currency_a += $reward->amount;
                    break;
                case 'CurrencyB':
                    $playerData->currency_b += $reward->amount;
                    break;
                case 'CurrencyC':
                    $playerData->currency_c += $reward->amount;
                    break;
                default:
                    return;
            }
        }
        else if ($reward->type === RewardType::Inventory) {
            $itemUuid = Uuid::fromString($reward->id)->toString();

            try {
                // Since we can only have one occurrence of an item in the inventory, and an exception gets thrown when we try to add the same item twice
                $playerData->inventory()->attach($itemUuid);
            } catch (UniqueConstraintViolationException $e) {

            }
        }
    }
}
