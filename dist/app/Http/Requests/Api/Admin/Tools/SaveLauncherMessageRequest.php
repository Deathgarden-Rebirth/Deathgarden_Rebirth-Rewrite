<?php

namespace App\Http\Requests\Api\Admin\Tools;

use Illuminate\Foundation\Http\FormRequest;

class SaveLauncherMessageRequest extends FormRequest
{
    public string $message;

    public ?string $url;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'message' => 'required|string',
            'url' => 'nullable|string',
        ];
    }

    protected function passedValidation()
    {
        $this->message = $this->input('message');
        $this->url = $this->input('url');
    }
}
