<?php

namespace App\APIClients\SteamAPIClient;

use App\APIClients\BaseApiClient;
use Illuminate\Support\Facades\Config;

abstract class SteamApiClient extends BaseApiClient
{
    // sets if the api key should be used for this request
    protected bool $useKey = true;

    protected string $endpoint;

    protected ?string $error = null;

    public function __construct()
    {
        parent::__construct();

        $this->url = config('services.steam.baseUrl').$this->endpoint;

        if($this->useKey) {
            $this->queryParams['key'] = Config::get('services.steam.apiKey');
        }
    }
}