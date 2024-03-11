<?php

namespace App\Http\Requests\Metrics;

use App\Enums\Game\Faction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MatchmakingRequest extends FormRequest
{
    public string $playerId;

    public ?string $matchId;

    public string $playerRole;

    public Faction $faction;

    public ?string $endState;

    public ?string $group;

    public ?string $region;

    public int $groupSize;

    public int $beginTime;

    public int $endTime;

    public string $eventType;

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
            'playerId' => 'required|string',
            'matchId' => 'present',
            'playerRole' => 'required|string',
            'faction' => ['required', Rule::enum(Faction::class)],
            'endState' => 'string',
            'group' => 'string',
            'region' => 'string',
            'groupSize' => 'required|int',
            'beginTime' => 'required|int',
            'endTime' => 'required|int',
            'eventType' => 'required|string'
        ];
    }

    protected function passedValidation()
    {
        $this->playerId = $this->input('playerId');
        $this->matchId = $this->input('matchId');
        $this->playerRole = $this->input('playerRole');
        $this->faction = Faction::tryFrom($this->input('faction'));
        $this->endState = $this->input('endState');
        $this->group = $this->input('group');
        $this->region = $this->input('region');
        $this->groupSize = $this->input('groupSize');
        $this->beginTime = $this->input('beginTime');
        $this->endTime = $this->input('endTime');
        $this->eventType = $this->input('eventType');
    }
}
