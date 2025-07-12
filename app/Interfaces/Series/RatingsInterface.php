<?php
// AI GENERATED

namespace App\Interfaces\Series;

interface RatingsInterface
{
    /**
     * Get the number of votes.
     *
     * @return int
     */
    public function getVotes(): int;

    /**
     * Get the rating value.
     *
     * @return float
     */
    public function getValue(): float;
}
