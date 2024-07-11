<?php

namespace App\Http\Requests\Api\Admin\Tools;

use App\Enums\Auth\Permissions;
use App\Http\Responses\Api\Player\Inbox\InboxMessageReward;
use Auth;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MailerSendRequest extends FormRequest
{
    public ?array $users;

    public bool $allUsers;

    public string $title;

    public string $body;

    public string $tag;

    public ?Carbon $expireAt;

    /** @var InboxMessageReward[] */
    public array $rewards = [];

    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->can(Permissions::INBOX_MAILER->value);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'body' => 'required|string',
            'tag' => 'required|string',
            'expireAt' => 'nullable|date',
            'rewards' => 'sometimes|array',
            'users' => [Rule::requiredIf(!$this->has('allUsers')), 'array'],
            'allUsers' => 'sometimes',
        ];
    }

    protected function passedValidation(): void
    {
        $this->title = $this->input('title');
        $this->body = $this->input('body');
        $this->tag = $this->input('tag');
        $this->expireAt = $this->input('expireAt') === null ? null : new Carbon($this->input('expireAt'));

        $this->users = $this->input('users');
        $this->allUsers = $this->has('allUsers');

        $rewardTypes = $this->input('rewards.type');
        $rewardIds = $this->input('rewards.id');
        $rewardAmounts = $this->input('rewards.amount');

        if($rewardIds === null)
            return;

        foreach ($rewardIds as $index => $id) {
            $this->rewards[] = new InboxMessageReward(
                $rewardTypes[$index],
                $rewardAmounts[$index],
                $id,
            );
        }


    }
}
