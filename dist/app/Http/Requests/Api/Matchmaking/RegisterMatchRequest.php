<?php

namespace App\Http\Requests\Api\Matchmaking;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class RegisterMatchRequest extends FormRequest
{
    public string $sessionSettings;

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
            'customData.SessionSettings' => 'required|string'
        ];
    }

    protected function passedValidation()
    {
        $this->sessionSettings = $this->input('customData.SessionSettings');
    }
}
