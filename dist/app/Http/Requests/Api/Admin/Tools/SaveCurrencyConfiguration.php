<?php

namespace App\Http\Requests\Api\Admin\Tools;

use Illuminate\Foundation\Http\FormRequest;

class SaveCurrencyConfiguration extends FormRequest
{
    public float $currencyAMultiplier;
    public float $currencyBMultiplier;
    public float $currencyCMultiplier;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'currencyA' => 'required|decimal:0,2',
            'currencyB' => 'required|decimal:0,2',
            'currencyC' => 'required|decimal:0,2',
        ];
    }

    protected function passedValidation()
    {
        $this->currencyAMultiplier = $this->input('currencyA');
        $this->currencyBMultiplier = $this->input('currencyB');
        $this->currencyCMultiplier = $this->input('currencyC');
    }
}
