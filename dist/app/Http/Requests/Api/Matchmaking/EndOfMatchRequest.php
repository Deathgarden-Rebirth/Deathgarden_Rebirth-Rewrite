<?php

namespace App\Http\Requests\Api\Matchmaking;

use App\Enums\Game\Faction;
use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EndOfMatchRequest extends FormRequest
{
    public array $players;

    public Faction $dominantFaction;

    public string $matchId;

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
            'data.players' => 'present|array',
            'data.dominantFaction' => ['required', Rule::enum(Faction::class)],
            'data.matchId' => 'required|string',
        ];
    }

    protected function passedValidation()
    {
        $this->players = $this->input('data.players');
        $this->dominantFaction = Faction::tryFrom($this->input('data.dominantFaction'));
        $this->matchId = $this->input('data.matchId');
    }
}
