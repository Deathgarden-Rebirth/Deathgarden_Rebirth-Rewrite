<?php

namespace App\Http\Responses\Api\Player\Purchase;

class WalletEntry implements \JsonSerializable
{
    public function __construct(
        public int $currencyA,
        public int $debitCurrencyA,
        public int $currencyB,
        public int $debitCurrencyB,
        public int $currencyC,
        public int $debitCurrencyC,
    )
    {
    }

    public function jsonSerialize(): mixed
    {
        $result = [];

        if($this->debitCurrencyA > 0)
            $result[] = [
                'balance' => $this->currencyA,
                'currency' => 'CurrencyA',
                'currencyGroup' => 'SoftCurrencyGroup',
                'debited' => $this->debitCurrencyA
            ];

        if($this->debitCurrencyB > 0)
            $result[] = [
                'balance' => $this->currencyB,
                'currency' => 'CurrencyB',
                'currencyGroup' => 'SoftCurrencyGroup',
                'debited' => $this->debitCurrencyB
            ];

        if($this->debitCurrencyC > 0)
            $result[] = [
                'balance' => $this->currencyC,
                'currency' => 'CurrencyC',
                'currencyGroup' => 'SoftCurrencyGroup',
                'debited' => $this->debitCurrencyC
            ];

        return $result;
    }
}