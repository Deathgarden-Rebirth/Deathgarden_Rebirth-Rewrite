<?php

namespace App\APIClients;


use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

abstract class BaseApiClient
{
    protected PendingRequest $request;

    protected string $url;

    protected HttpMethod $method;

    protected Collection $body;

    protected array $queryParams = [];

    protected array $headers = [];

    protected bool $debug = false;

    public function __construct()
    {
        $this->body = collect();
    }

    protected function dispatch(): Response
    {
        $this->request = Http::withBody($this->body)->asJson()->withQueryParameters($this->queryParams)->withHeaders($this->headers);

        if($this->debug)
            $this->request->dump();

        return $this->request->{$this->method->value}($this->url);
    }
}