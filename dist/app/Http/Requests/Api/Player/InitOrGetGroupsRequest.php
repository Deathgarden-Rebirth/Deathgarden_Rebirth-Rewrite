<?php

namespace App\Http\Requests\Api\Player;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class InitOrGetGroupsRequest extends FormRequest
{
    public bool $skipProgressionGroups;

    public bool $skipMetadataGroups;


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
            'data.skipProgressionGroups' => 'bool|required',
            'data.skipMetadataGroups' => 'bool|required',
        ];
    }

    protected function passedValidation()
    {
        $this->skipProgressionGroups = $this->input('data.skipProgressionGroups');
        $this->skipMetadataGroups = $this->input('data.skipMetadataGroups');
    }
}
