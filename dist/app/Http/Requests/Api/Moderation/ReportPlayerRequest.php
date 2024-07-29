<?php

namespace App\Http\Requests\Api\Moderation;

use App\Enums\Game\CharacterState;
use App\Enums\Game\Faction;
use App\Enums\Game\PlayerReportReason;
use App\Models\User\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class ReportPlayerRequest extends FormRequest
{
    public string $type;

    public User $reportedPlayer;

    public string $platform;

    public PlayerReportReason $reason;

    public string $details;

    public string $matchId;

    /** @var Collection<ReportPlayerInfoListEntry>  */
    public Collection $playerInfos;


    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return \Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'required|string',
            'entityId' => 'required|string|exists:'.User::class.',id',
            'platformId' => 'required|string',
            'reason' => ['required', Rule::enum(PlayerReportReason::class)],
            'details' => 'required|string',
            'gameSpecificData.matchId' => 'required|string',
            'gameSpecificData.playerInfoList.*.playerId' => 'required|string',
            'gameSpecificData.playerInfoList.*.characterState' => ['required', Rule::enum(CharacterState::class)],
            'gameSpecificData.playerInfoList.*.faction' => ['required', Rule::enum(Faction::class)],
            'gameSpecificData.playerInfoList.*.totalXpEarned' => 'required|int',
            'gameSpecificData.playerInfoList.*.playtimeInSec' => 'required|numeric',
            'gameSpecificData.playerInfoList.*.isReportedPlayer' => 'required|bool',
            'gameSpecificData.playerInfoList.*.isReporterPlayer' => 'required|bool',

        ];
    }

    protected function passedValidation()
    {
        $this->type = $this->input('type');
        $this->reportedPlayer = User::find($this->input('entityId'));
        $this->platformId = $this->input('platformId');
        $this->reason = PlayerReportReason::tryFrom($this->input('reason'));
        $this->details = $this->input('details');

        $this->matchId = $this->input('gameSpecificData.matchId');
        $playerInfos = $this->input('gameSpecificData.playerInfoList');
        $this->playerInfos = collect();

        foreach ($playerInfos as $playerInfo) {
            $this->playerInfos->add(ReportPlayerInfoListEntry::makeFromArray($playerInfo));
        }
    }
}
