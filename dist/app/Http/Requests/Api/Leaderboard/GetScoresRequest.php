<?php

namespace App\Http\Requests\Api\Leaderboard;

use App\Enums\Game\Faction;
use App\Enums\Game\Leaderboard\Window;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class GetScoresRequest extends FormRequest
{
    /**
     * @var string[];
     */
    public array $playerIds;

    public Faction $faction;

    public int $top;

    public Window $window;

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
            'playerIds' => ['required', 'array'],
            'playerIds.*' => 'string',
            'faction' => ['required', new Enum(Faction::class)],
            'top' => ['required', 'integer',],
            'window' => ['required', new Enum(Window::class)],
        ];
    }

    public function passedValidation(): void
    {
        $this->playerIds = $this->input('playerIds');
        $this->faction = Faction::tryFrom($this->input('faction'));
        $this->top = (int)$this->input('top');
        $this->window = Window::tryFrom($this->input('window'));
    }
}
