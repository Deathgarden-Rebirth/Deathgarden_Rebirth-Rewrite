<?php

namespace App\Http\Responses\Api\Player;

class CurrencyResponse implements \JsonSerializable
{
    public function __construct(
        public int $currencyA,
        public int $currencyB,
        public int $currencyC,
    )
    {
    }

    public function jsonSerialize(): mixed
    {
        return [
            'list' => [
                [
                    'balance' => $this->currencyA,
                    'currency' => 'CurrencyA',
                    'currencyGroup' => 'SoftCurrencyGroup',
                ],
                [
                    'balance' => $this->currencyB,
                    'currency' => 'CurrencyB',
                    'currencyGroup' => 'SoftCurrencyGroup',
                ],
                [
                    'balance' => $this->currencyC,
                    'currency' => 'CurrencyC',
                    'currencyGroup' => 'SoftCurrencyGroup',
                ],
            ]
        ];
    }
}