<?php

namespace App\Http\Requests\Api\Admin\Tools;

use App\APIClients\HttpMethod;
use App\Enums\Auth\Permissions;
use App\Enums\Game\Faction;
use App\Enums\Game\Message\GameNewsRedirectMode;
use App\Enums\Game\Message\MessageType;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SubmitGameNewsRequest extends FormRequest
{
    const ENABLED = 'enabled';

    const TITLE = 'title';

    const DESCRIPTION = 'description';

    const MESSSAGE_TYPE = 'message-type';

    const FACTION = 'faction';

    const ONE_TIME_NEWS = 'one-time-news';

    const QUIT_GAME = 'quit-game';

    const COMPLETE_ONE_MATCH = 'one-match';

    const REDIRECT_MODE = 'redirect-mode';

    const REDIRECT_ITEM = 'redirect-item';

    const REDIRECT_URL = 'redirect-url';

    const POP_UP_BACKGROUND = 'pop-up-bg';

    const IN_GAME_BACKGROUND = 'in-game-bg';

    const IN_GAME_THUMBNAIL = 'in-game-thumbnail';

    const FROM_DATE = 'from-date';

    const TO_DATE = 'to-date';

    const DISPLAY_X_TIMES = 'display-x-times';

    const MAX_PLAYER_LEVEL = 'max-player-level';

    const SUBMIT_METHOD = 'submit-method';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->can(Permissions::GAME_NEWS->value);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $submitMethod = HttpMethod::tryFrom($this->input(static::SUBMIT_METHOD));

        if ($submitMethod === HttpMethod::DELETE)
            return [
                static::SUBMIT_METHOD => ['required', Rule::enum(HttpMethod::class)],
            ];

        return [
            static::TITLE => 'required|string',
            static::DESCRIPTION => 'required|string',
            static::MESSSAGE_TYPE => ['required', Rule::enum(MessageType::class)],
            static::FACTION => ['nullable', Rule::enum(Faction::class)],
            static::REDIRECT_MODE => ['nullable', Rule::enum(GameNewsRedirectMode::class)],
            static::REDIRECT_ITEM => 'nullable|string',
            static::REDIRECT_URL => 'nullable|string',
            static::POP_UP_BACKGROUND => 'nullable|string',
            static::IN_GAME_BACKGROUND => 'nullable|string',
            static::IN_GAME_THUMBNAIL => 'nullable|string',
            static::FROM_DATE => 'date|required',
            static::TO_DATE => 'date|required',
            static::DISPLAY_X_TIMES => 'integer|nullable',
            static::MAX_PLAYER_LEVEL => 'integer|nullable',
            static::SUBMIT_METHOD => ['required', Rule::enum(HttpMethod::class)],
        ];
    }
}
