<?php

namespace App\Http\Requests\Api\Admin\Tools;

use Illuminate\Foundation\Http\FormRequest;

class SaveVersioningRequest extends FormRequest
{
    public string $launcherVersion;

    public string $gameVersion;

    public string $contentVersion;

    public string $catalogVersion;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'launcherVersion' => 'required|string',
            'gameVersion' => 'required|string',
            'contentVersion' => 'required|string',
            'catalogVersion' => 'required|string',
        ];
    }

    protected function passedValidation()
    {
        $this->launcherVersion = $this->input('launcherVersion');
        $this->gameVersion = $this->input('gameVersion');
        $this->contentVersion = $this->input('contentVersion');
        $this->catalogVersion = $this->input('catalogVersion');
    }
}
