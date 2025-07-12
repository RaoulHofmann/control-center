<?php
// AI GENERATED

namespace App\Interfaces\Series;

interface SeasonInterface
{
    /**
     * Get the season number.
     *
     * @return int
     */
    public function getSeasonNumber(): int;

    /**
     * Check if the season is monitored.
     *
     * @return bool
     */
    public function isMonitored(): bool;

    /**
     * Get the season statistics.
     *
     * @return SeasonStatisticsInterface
     */
    public function getStatistics(): SeasonStatisticsInterface;

    /**
     * Get the season images.
     *
     * @return array<ImageInterface>
     */
    public function getImages(): array;
}
