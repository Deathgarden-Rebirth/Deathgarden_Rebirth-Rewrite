<?php

namespace App\Http\Requests\Api\Player;

use App\Enums\Game\Characters;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;

class ResetCharacterProgressionForPrestigeRequest extends FormRequest
{
    public Characters $character;

    public string $characterCatalogId;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'data.characterId' => 'required|string'
        ];
    }

    protected function passedValidation(): void
    {
        $this->characterCatalogId = $this->input('data.characterId');
        $foundCharacter = Characters::tryFromUuid(Uuid::fromString($this->characterCatalogId));

        if ($foundCharacter === null)
            throw ValidationException::withMessages(['data.characterId' => 'Id does not belong to a valid character.']);

        $this->character = $foundCharacter;
    }
}
