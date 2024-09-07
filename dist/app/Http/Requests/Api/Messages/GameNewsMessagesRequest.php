<?php

namespace App\Http\Requests\Api\Messages;

use App\Enums\Game\Faction;
use App\Enums\Game\Message\MessageType;
use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GameNewsMessagesRequest extends FormRequest
{
    public bool $sortDescending;

    public int $gameVersion;

    public string $platform;

    public string $language;

    public MessageType $messageType;

    public Faction $faction;

    public int $playerLevel;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sortDesc' => 'string',
            'gameVersion' => 'int',
            'platform' => 'string',
            'language' => 'string',
            'messageType' => ['required', Rule::enum(MessageType::class)],
            'faction' => [Rule::enum(Faction::class)],
            'playerLevel' => 'int'
        ];
    }

    protected function passedValidation()
    {
        $this->sortDescending = (bool)$this->input('sortDesc', true);
        $this->gameVersion = (int)$this->input('gameVersion', 0);
        $this->platform = $this->input('platform', 'None');
        $this->language = $this->input('language', 'EN');
        $this->messageType = MessageType::tryFrom($this->input('messageType'));
        $this->faction = Faction::tryFrom($this->input('faction', 'None'));
        $this->playerLevel = (int)$this->input('playerLevel');
    }
}
