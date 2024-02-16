<?php

namespace App\APIClients\SteamAPIClient\ISteamUser\GetPlayerSummaries;

use App\APIClients\HttpMethod;
use App\APIClients\SteamAPIClient\Exceptions\SteamRequestException;
use App\APIClients\SteamAPIClient\SteamApiClient;
use Illuminate\Support\Arr;

class GetPlayerSummariesRequest extends SteamApiClient
{
    protected string $endpoint = 'ISteamUser/GetPlayerSummaries/v2/';

    protected HttpMethod $method = HttpMethod::GET;

    protected bool $useKey = true;

    public function __construct(
        public array $steamIds,
    )
    {
        parent::__construct();
    }

    public function getPlayerSummaries(): GetPlayerSummariesResponse|false
    {
        $this->queryParams['steamids'] = implode(',', $this->steamIds);

        $response = $this->dispatch();

        if(!$response->successful()) {
            $this->error = 'could not get name of Players';
            return false;
        }

        try {
            return $this->parseResponse($response->body());
        }
        catch (SteamRequestException $e) {
            $this->error = $e->getMessage();
            return false;
        }

    }

    private function parseResponse(string $body) {
        $json = json_decode($body);

        if(!isset($json->response->players)) {
            if(!isset($json->response->error))
                throw new SteamRequestException('GetPlayerSummariesRequest: Unknown Error while connecting to Steam.');

            $error = $json->response->error;
            throw new SteamRequestException('GetPlayerSummariesRequest: Steam API Response resulted in Error: '.$error->errorcode.', '.$error->errordesc);
        }

        return new GetPlayerSummariesResponse((array)$json->response->players);
    }
}