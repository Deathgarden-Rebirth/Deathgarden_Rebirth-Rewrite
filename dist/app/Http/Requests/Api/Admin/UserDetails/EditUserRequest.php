<?php

namespace App\Http\Requests\Api\Admin\UserDetails;

use App\Enums\Auth\Permissions;
use App\Enums\Game\Faction;
use App\Enums\Game\Hunter;
use App\Enums\Game\Runner;
use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditUserRequest extends FormRequest
{
    public int $currencyA;
    public int $currencyB;
    public int $currencyC;
    public Faction $lastFaction;
    public Runner $lastRunner;
    public Hunter $lastHunter;
    public bool $hasPlayedDG1 = false;
    public int $runnerFactionLevel;
    public int $hunterFactionLevel;
    public int $levelSmoke;
    public int $levelInk;
    public int $levelGhost;
    public int $levelSawbones;
    public int $levelSwitch;
    public int $levelDash;
    public int $levelStalker;
    public int $levelPoacher;
    public int $levelInquisitor;
    public int $levelMass;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if(!Auth::check())
            return false;

        return Auth::user()->can(Permissions::EDIT_USERS->value);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'currencyA' => 'int|required',
            'currencyB' => 'int|required',
            'currencyC' => 'int|required',
            'lastFaction' => ['required', Rule::enum(Faction::class)],
            'lastRunner' => ['required', Rule::enum(Runner::class)],
            'lastHunter' => ['required', Rule::enum(Hunter::class)],
            'runnerFactionLevel' => 'int|min:1',
            'SmokeLevel' => 'int|min:1',
            'InkLevel' => 'int|min:1',
            'GhostLevel' => 'int|min:1',
            'SawbonesLevel' => 'int|min:1',
            'SwitchLevel' => 'int|min:1',
            'DashLevel' => 'int|min:1',
            'hunterFactionLevel' => 'int|min:1',
            'StalkerFactionLevel' => 'int|min:1',
            'PoacherFactionLevel' => 'int|min:1',
            'InquisitorFactionLevel' => 'int|min:1',
            'MassLevel' => 'int|min:1',
        ];
    }

    public function passedValidation()
    {
        $this->currencyA = $this->input('currencyA');
        $this->currencyB = $this->input('currencyB');
        $this->currencyC = $this->input('currencyC');

        $this->lastFaction = Faction::tryFrom($this->input('lastFaction'));
        $this->lastRunner = Runner::tryFrom($this->input('lastRunner'));
        $this->lastHunter = Hunter::tryFrom($this->input('lastHunter'));

        $this->hasPlayedDG1 = $this->input('hasPlayedDG1', false);

        $this->runnerFactionLevel = $this->input('runnerFactionLevel');

        foreach(Runner::cases() as $runner) {
            $this->{'level'.$runner->value} = $this->input($runner->value.'Level');
        }

        $this->hunterFactionLevel = $this->input('hunterFactionLevel');

        foreach (Hunter::cases() as $hunter) {
            $this->{'level'.$hunter->value} = $this->input($hunter->value.'Level');
        }
    }
}
