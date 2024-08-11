<?php

namespace App\Http\Requests\Api\Admin\Tools;

use App\Enums\Auth\Permissions;
use Auth;
use Illuminate\Foundation\Http\FormRequest;

class HandleModerationRequest extends FormRequest
{
    public string $consequences;

    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'consequences' => 'required|string'
        ];
    }

    protected function passedValidation()
    {
        $this->consequences = $this->input('consequences');
    }
}