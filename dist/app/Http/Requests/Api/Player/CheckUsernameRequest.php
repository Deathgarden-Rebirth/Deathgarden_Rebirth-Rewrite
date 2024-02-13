<?php

namespace App\Http\Requests\Api\Player;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CheckUsernameRequest extends FormRequest
{
    public readonly string $username;

    public readonly string $userId;

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
            'userId' => 'required|string',
            'username' => 'required|string',
        ];
    }

    public function passedValidation(): void
    {
        $this->username = $this->input('username');
        $this->userId = $this->input('userId');
    }
}
