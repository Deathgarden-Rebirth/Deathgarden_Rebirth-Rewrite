<?php

namespace App\APIClients\SteamAPIClient\ISteamUserAuth\AuthenticateTicket;

use App\APIClients\HttpMethod;
use App\APIClients\SteamAPIClient\Exceptions\SteamRequestException;
use App\APIClients\SteamAPIClient\SteamApiClient;
use Exception;
use Illuminate\Support\Facades\Config;

class AuthenticateTicketRequest extends SteamApiClient
{
    protected string $endpoint = 'ISteamUserAuth/AuthenticateUserTicket/v1/';

    protected HttpMethod $method = HttpMethod::GET;

    protected bool $useKey = true;

    protected ?string $error = null;

    public function __construct(
        public string $ticket,
        public ?string $identity = null,
    )
    {
        parent::__construct();

        $this->queryParams['appid'] = Config::get('services.steam.appID');
    }

    /**
     * Authenticates the Session Ticket with Steam
     *
     * @return AuthenticateTicketResponse|false returns the Response when successful, false otherwise
     */
    public function authenticateTicket(): AuthenticateTicketResponse|false {
        $this->queryParams['ticket'] = $this->ticket;
        if($this->identity)
            $this->queryParams['identity'] = $this->identity;

        $response = $this->dispatch();

        if(!$response->successful()) {
            $this->error = 'Could not authenticate with Steam.';
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

    /**
     * @param string $body
     *
     * @return AuthenticateTicketResponse
     * @throws Exception
     */
    private function parseResponse(string $body): AuthenticateTicketResponse
    {
        $json = json_decode($body);

        if(!isset($json->response->params)) {
            if(!isset($json->response->error))
                throw new SteamRequestException('AuthenticateTicketRequest: Unknown Error while connecting to Steam.');

            $error = $json->response->error;
            throw new SteamRequestException('AuthenticateTicketRequest: Steam API Response resulted in Error: '.$error->errorcode.', '.$error->errordesc);
        }

        $params = $json->response->params;

        return new AuthenticateTicketResponse(
            $params->steamid,
            $params->ownersteamid,
            $params->vacbanned,
            $params->publisherbanned
        );
    }

    public function getError(): ?string
    {
        return $this->error;
    }
}