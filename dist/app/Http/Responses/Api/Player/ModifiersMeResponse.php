<?php

namespace App\Http\Responses\Api\Player;

class ModifiersMeResponse
{
    public string $TokenId;

    public string $UserId;

    public array $RoleIds = [
        '755D4DFE-40DA1512-B01E3D8C-FF3C8D4D',
        'C50FFFBF-46866131-82F45890-651797CE',
    ];

    public function __construct(string $userId, string $token)
    {
        $this->TokenId = $token;
        $this->UserId = $userId;
    }
}