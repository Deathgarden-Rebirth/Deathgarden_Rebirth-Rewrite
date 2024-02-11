<?php

namespace App\Http\Responses\Api\Auth;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class SteamLoginResponse
{
    public string $preferredLanguage = 'en';

    public Platform $friendsFirstSync;

    public Platform $fixedMyFriendsUserPlatformId;

    public string $id;

    public AuthUserProvider $provider;

    /**
     * @var AuthProvider[]
     */
    public array $providers;

    public array $friends = [];

    public TriggerResults $triggerResults;

    public string $tokenId;

    public int $generated;

    public int $expire;

    public string $userId;

    public string $token;

    public function __construct(string $userId, int $steamId)
    {
        $this->friendsFirstSync = new Platform();
        $this->fixedMyFriendsUserPlatformId = new Platform();
        $this->id = $userId;
        $this->provider = new AuthUserProvider($steamId, $userId);
        $this->providers = [new AuthProvider($steamId)];
        $this->triggerResults = new TriggerResults();
        $this->tokenId = $userId;
        $this->generated = Carbon::now()->getTimestamp();
        $this->expire = Carbon::now()->addMinutes(Config::get('session.lifetime', 120))->getTimestamp();
        $this->userId = $userId;
    }
}

class Platform {
    public bool $steam;

    public function __construct(bool $steam = true)
    {
        $this->steam = $steam;
    }
}

class AuthProvider {
    public int $providerId;

    public string $providerName;

    public function __construct(int $providerId, string $providerName = 'steam')
    {
        $this->providerId = $providerId;
        $this->providerName = $providerName;
    }
}

class AuthUserProvider extends AuthProvider {
    public string $userId;

    public function __construct(int $providerId, string $userId, string $providerName = 'steam')
    {
        parent::__construct($providerId, $providerName);
        $this->userId = $userId;
    }
}

class TriggerResults {
    public array $success = [];
    public array $error = [];
}