<?php

namespace App\Http\Requests\Api\Player;

use App\Enums\Game\ChallengeType;
use App\Models\User\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetChallengesRequest extends FormRequest
{
    public User $user;

    public ChallengeType $type;

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
            'data.userId' => 'required|exists:users,id',
            'data.challengeType' => ['required', Rule::enum(ChallengeType::class)],
        ];
    }

    public function passedValidation(): void
    {
        $this->user = User::find($this->input('data.userId'));
        $this->type = ChallengeType::tryFrom($this->input('data.challengeType'));
    }
}
