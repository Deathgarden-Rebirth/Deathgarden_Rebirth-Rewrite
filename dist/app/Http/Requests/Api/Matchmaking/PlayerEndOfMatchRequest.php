<?php

namespace App\Http\Requests\Api\Matchmaking;

use App\Enums\Game\CharacterState;
use App\Enums\Game\Faction;
use App\Enums\Game\ItemGroupType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PlayerEndOfMatchRequest extends FormRequest
{
    public string $playerId;

    public Faction $faction;

    public ItemGroupType $characterGroup;

    public int $playtime;

    public string $platform;

    public bool $hasQuit;

    public CharacterState $characterState;

    public string $matchId;

    public string $gamemode;

    public array $experienceEvents;

    public array $earnedCurrencies;

    public array $completedChallenges;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'data.playerId' => 'required|string',
            'data.faction' => ['required', Rule::enum(Faction::class)],
            'data.characterGroup' => ['required', Rule::enum(ItemGroupType::class)],
            'data.playtime' => 'required|int',
            'data.platform' => 'string',
            'data.hasQuit' => 'required|bool',
            'data.characterState' => ['required', Rule::enum(CharacterState::class)],
            'data.matchId' => 'required|string',
            'data.matchGameMode' => 'required|string',
            'data.experienceEvents' => 'present|array',
            'data.earnedCurrencies' => 'present|array',
            'data.completedChallenges' => 'present|array',
        ];
    }

    protected function passedValidation()
    {
        $this->playerId = $this->input('data.playerId');
        $this->faction = Faction::tryFrom($this->input('data.faction'));
        $this->characterGroup = ItemGroupType::tryFrom($this->input('data.characterGroup'));
        $this->playtime = $this->input('data.playtime', 0);
        $this->platform = $this->input('data.platform', 'None');
        $this->hasQuit = $this->input('data.hasQuit');
        $this->characterState = CharacterState::tryFrom($this->input('data.characterState'));
        $this->matchId = $this->input('data.matchId');
        $this->gamemode = $this->input('data.matchGameMode');
        $this->experienceEvents = $this->input('data.experienceEvents');
        $this->earnedCurrencies = $this->input('data.earnedCurrencies');
        $this->completedChallenges = $this->input('data.completedChallenges');
    }
}
