<?php

namespace App\Http\Responses\Api\Player;

use App\Models\User\Ban;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;

class GetBanStatusResponse
{
    public bool $IsBanned = false;

    public BanInfo $BanInfo;

    public function __construct(bool $isBanned = false, ?Ban $ban= null)
    {
        $this->IsBanned = $isBanned;

        if($ban !== null) {
            $this->BanInfo = new BanInfo($ban->ban_reason, $ban->start_date, $ban->end_date);
        }
    }
}

class BanInfo {
    public int $BanPeriod = 0;

    public string $BanReason;

    public int $BanStart = 0;

    public int $BanEnd = 0;

    public bool $Confirmed = true;

    public bool $Pending = false;

    public function __construct(string $banReason = 'Not Banned',?Carbon $startDate = null, ?Carbon $endDate = null)
    {
        $this->BanReason = $banReason;

        if(isset($startDate) && isset($endDate))
            $this->BanStart = $startDate->getTimestamp();
            $this->BanEnd = $endDate->getTimestamp();
            $this->BanPeriod = $startDate->diffInDays($endDate);
    }
}
