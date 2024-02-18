<?php

namespace App\Http\Responses\Api\Eula;

class EulaConsentResponse
{
    public bool $IsGiven;

    public string $ConsentId = 'eula2';

    public int $UpdateDate = 1689714606;

    public bool $AttentionNeeded = false;

    public ConsentVersion $LatestVersion;

    public string $UserId;

    public function __construct(string $userId, bool $isGiven = false)
    {
        $this->IsGiven = $isGiven;
        $this->UserId = $userId;
        $this->LatestVersion = new ConsentVersion();
    }
}

class ConsentVersion {
    public function __construct(
        public string $Label = "eula2",
        public int $EntryDate = 1689714606
    )
    {
    }
}