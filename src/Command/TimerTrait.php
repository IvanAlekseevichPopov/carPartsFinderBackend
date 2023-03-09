<?php

declare(strict_types=1);

namespace App\Command;

trait TimerTrait
{
    private int $endTime;

    /**
     * @param int $ttl Time to live in seconds
     */
    public function startTimer(int $ttl): int
    {
        $this->endTime = time() + (int) ($ttl * 0.92);

        return $this->endTime;
    }

    public function isTimeOut(): bool
    {
        $now = time();

        return $now > $this->endTime;
    }
}
