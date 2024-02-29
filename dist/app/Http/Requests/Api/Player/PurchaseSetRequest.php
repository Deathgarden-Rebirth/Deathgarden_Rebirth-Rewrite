<?php

namespace App\Http\Requests\Api\Player;

use Illuminate\Foundation\Http\FormRequest;
use Ramsey\Uuid\Uuid;

class PurchaseSetRequest extends FormRequest
{
    public string $itemId;

    public string $currencyGroup;

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
            'data.itemId' => 'required|string',
            'data.currencyGroup' => 'required|string'
        ];
    }

    protected function passedValidation()
    {
        $this->itemId = Uuid::fromString($this->input('data.itemId'))->toString();
        $this->currencyGroup = $this->input('data.currencyGroup');
    }
}
