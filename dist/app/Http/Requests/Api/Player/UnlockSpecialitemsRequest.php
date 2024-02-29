<?php

namespace App\Http\Requests\Api\Player;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UnlockSpecialitemsRequest extends FormRequest
{
    public array $appIds;

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
            'data.additionalSteamAppIds' => 'present|array',
        ];
    }

    protected function passedValidation()
    {
        $this->appIds = $this->input('data.additionalSteamAppIds');
    }
}
