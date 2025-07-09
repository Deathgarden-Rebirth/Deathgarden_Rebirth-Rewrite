<?php

namespace App\Http\Requests\Api\Admin\Tools;

use Illuminate\Foundation\Http\FormRequest;

class SaveMatchmakingConfigurationRequest extends FormRequest
{
    public int $matchmakingWaitingTime;

    public function rules(): array {
        return [
            'matchmakingWaitingTime' => ['required', 'integer'],
        ];
    }

    protected function passedValidation()
    {
        $this->matchmakingWaitingTime = (int)$this->input('matchmakingWaitingTime');
    }
}