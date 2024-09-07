<?php

namespace App\Http\Requests\Api\Player;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ExecuteChallengeProgressionBatchRequest extends FormRequest
{
    public string $userId;

    public array $operations;

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
            'data.userId' => 'required|string',
            'data.operations' => 'present|array'
        ];
    }

    protected function passedValidation()
    {
        $this->userId = $this->input('data.userId');
        $this->operations = $this->input('data.operations');
    }
}
