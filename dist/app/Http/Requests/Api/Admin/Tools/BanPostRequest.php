<?php

namespace App\Http\Requests\Api\Admin\Tools;

use App\APIClients\HttpMethod;
use App\Enums\Auth\Permissions;
use Auth;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BanPostRequest extends FormRequest
{
    public string $reason;

    public Carbon $startDate;

    public Carbon $endDate;

    public HttpMethod $editMethod;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()?->can(Permissions::EDIT_USERS->value);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'method' => [Rule::enum(HttpMethod::class), 'required'],
            'reason' => 'string|required',
            'startDate' => 'date|required',
            'endDate' => 'date|required',
        ];
    }

    public function passedValidation()
    {
        $fromTimezone = 'Europe/Berlin';

        $this->reason = $this->input('reason');
        $this->startDate = Carbon::parse($this->input('startDate'), $fromTimezone);
        $this->endDate = Carbon::parse($this->input('endDate'), $fromTimezone);
        $this->editMethod = HttpMethod::from($this->input('method'));
    }
}
