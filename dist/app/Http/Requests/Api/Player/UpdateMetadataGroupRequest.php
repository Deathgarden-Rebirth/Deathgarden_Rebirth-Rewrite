<?php

namespace App\Http\Requests\Api\Player;

use App\Enums\Api\UpdateMetadataReason;
use App\Enums\Game\MetadataGroup;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateMetadataGroupRequest extends FormRequest
{
    public MetadataGroup $group;

    public ?UpdateMetadataReason $reason;

    public int $version;

    public array $metadata;

    public ?string $playerId;

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
            'data.reason' => 'required',
            'data.version' => 'required|int',
            'data.metadata' => 'required|array',
            'data.objectId' => ['required', Rule::enum(MetadataGroup::class)]
        ];
    }

    protected function passedValidation()
    {
        $this->group = MetadataGroup::tryFrom($this->input('data.objectId'));
        $this->version = $this->input('data.version');
        $this->metadata = $this->input('data.metadata');
        $this->playerId = $this->input('data.playerId');

        $this->reason = UpdateMetadataReason::tryFrom($this->input('data.reason'));
        if ($this->reason === null) {
            $message = 'Update Metadata with unknown reason: '.$this->input('data.reason');
            Log::channel('dg_requests_errors')->warning($message."\n".$this->input('data'));
        }
    }
}
