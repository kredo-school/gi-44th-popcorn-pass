<?php
// app/Services/TierService.php

namespace App\Services;

class TierService
{
    /**
     * Point thresholds for each tier.
     * Provisional values — confirm with teacher/team later,
     * derived from Figma's "600 pts = Platinum" reference point.
     */
    private const THRESHOLDS = [
        'platinum' => 600,
        'gold'     => 400,
        'silver'   => 200,
        'bronze'   => 0,
    ];

    /**
     * Ordered list of tiers from lowest to highest.
     */
    private const ORDER = ['bronze', 'silver', 'gold', 'platinum'];

    public function tierForPoints(int $points): string
    {
        foreach (self::THRESHOLDS as $tier => $minPoints) {
            if ($points >= $minPoints) {
                return $tier;
            }
        }

        return 'bronze';
    }

    public function nextTier(string $currentTier): ?string
    {
        $index = array_search($currentTier, self::ORDER, true);

        if ($index === false || $index === count(self::ORDER) - 1) {
            return null; // already at the top tier
        }

        return self::ORDER[$index + 1];
    }

    public function pointsToNextTier(int $points): ?int
    {
        $currentTier = $this->tierForPoints($points);
        $next = $this->nextTier($currentTier);

        if ($next === null) {
            return null;
        }

        return self::THRESHOLDS[$next] - $points;
    }

    /**
     * Progress (0-100) toward the next tier, for the
     * "Membership Journey" progress bar on the Rewards Dashboard.
     */
    public function progressPercent(int $points): int
    {
        $currentTier = $this->tierForPoints($points);
        $next = $this->nextTier($currentTier);

        if ($next === null) {
            return 100; // maxed out at Platinum
        }

        $currentMin = self::THRESHOLDS[$currentTier];
        $nextMin = self::THRESHOLDS[$next];

        $progress = ($points - $currentMin) / ($nextMin - $currentMin) * 100;

        return (int) min(100, max(0, round($progress)));
    }

    public function allTiers(): array
    {
        return self::ORDER;
    }
}