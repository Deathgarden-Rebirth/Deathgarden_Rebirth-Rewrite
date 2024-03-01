<?php

namespace App\Http\Requests\Api\Player;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

class PurchaseItemRequest extends FormRequest
{
    public string $objectId;

    public int $oldQuantity;

    public int $wantedQuantity;

    public string $currencyGroup;

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
            'data.objectId' => 'required|string',
            'data.oldQuantity' => 'required|int',
            'data.wantedQuantity' => 'required|int',
            'data.currencyGroup' => 'required|string',
        ];
    }

    protected function passedValidation()
    {
        $this->objectId = Uuid::fromString($this->input('data.objectId'))->toString();
        $this->oldQuantity = $this->input('data.oldQuantity');
        $this->wantedQuantity = $this->input('data.wantedQuantity');
        $this->currencyGroup  =$this->input('data.currencyGroup');
    }
}
