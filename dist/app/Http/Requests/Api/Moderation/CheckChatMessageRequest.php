<?php

namespace App\Http\Requests\Api\Moderation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CheckChatMessageRequest extends FormRequest
{
    public string $userId;

    public string $language;

    public string $message;

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
            'language' => 'required|string',
            'message' => 'required|string',
        ];
    }

    protected function passedValidation()
    {
        $this->userId = $this->input('userId');
        $this->language = $this->input('language');
        $this->message = $this->input('message');
    }
}
