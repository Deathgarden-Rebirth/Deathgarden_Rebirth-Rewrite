<?php

namespace App\Http\Requests\Api\Admin\Tools;

use Illuminate\Foundation\Http\FormRequest;

class SaveMatchConfigurationRequest extends FormRequest
{
    public float $constructDefeatsMultiplier;
    public float $downingMultiplier;
    public float $dronesMultiplier;
    public float $executionMultiplier;
    public float $gardenFinaleMultiplier;
    public float $hackingMultiplier;
    public float $hunterCloseMultiplier;
    public float $resourcesMultiplier;
    public float $teamActionsMultiplier;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'construct-defeats' => 'required|decimal:0,2',
            'downing' => 'required|decimal:0,2',
            'drones' => 'required|decimal:0,2',
            'execution' => 'required|decimal:0,2',
            'garden-finale' => 'required|decimal:0,2',
            'hacking' => 'required|decimal:0,2',
            'hunter-close' => 'required|decimal:0,2',
            'resources' => 'required|decimal:0,2',
            'team-actions' => 'required|decimal:0,2',
        ];
    }

    protected function passedValidation()
    {
        $this->constructDefeatsMultiplier = $this->input('construct-defeats');
        $this->downingMultiplier = $this->input('downing');
        $this->dronesMultiplier = $this->input('drones');
        $this->executionMultiplier = $this->input('execution');
        $this->gardenFinaleMultiplier = $this->input('garden-finale');
        $this->hackingMultiplier = $this->input('hacking');
        $this->hunterCloseMultiplier = $this->input('hunter-close');
        $this->resourcesMultiplier = $this->input('resources');
        $this->teamActionsMultiplier = $this->input('team-actions');
    }
}
