<?php

namespace App\Http\Requests\Api\Player\Inbox;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class ClaimInboxMessageRequest extends FormRequest
{
    public string $recipientId;

    public int $receivedTimestamp;

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
            'received' => 'required|int',
            'recipientId' => 'required|string',
        ];
    }

    public function passedValidation(): void
    {
        $this->recipientId = $this->input('recipientId');
        $this->receivedTimestamp = $this->input('received');
    }

}
