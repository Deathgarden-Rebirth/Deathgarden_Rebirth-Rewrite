<?php

namespace App\Http\Requests\Api\Matchmaking;

use App\Enums\Game\Matchmaking\MatchmakingSide;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class QueueRequest extends FormRequest
{
    public readonly string $category;

    public readonly MatchmakingSide $side;

    public readonly array $additionalUserIds;

    public readonly bool $checkOnly;

    public readonly string $region;

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
            'category' => 'required|string',
            'side' => ['required', Rule::enum(MatchmakingSide::class)],
            'additionalUserIds' => 'present|array',
            'checkOnly' => 'required|bool',
            'region' => 'string',
        ];
    }

    protected function passedValidation()
    {
        $this->category = $this->input('category');
        $this->side = MatchmakingSide::tryFrom($this->input('side'));
        $this->additionalUserIds = $this->input('additionalUserIds');
        $this->checkOnly = $this->input('checkOnly');
        $this->region = $this->input('region', 'None');
    }
}
