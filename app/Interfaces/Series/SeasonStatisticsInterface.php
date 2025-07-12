<?php
// AI GENERATED

namespace App\Interfaces\Series;

interface SeasonStatisticsInterface
{
    /**
     * Get the next airing date.
     *
     * @return string|null
     */
    public function getNextAiring(): ?string;

    /**
     * Get the previous airing date.
     *
     * @return string|null
     */
    public function getPreviousAiring(): ?string;

    /**
     * Get the episode file count.
     *
     * @return int
     */
    public function getEpisodeFileCount(): int;

    /**
     * Get the episode count.
     *
     * @return int
     */
    public function getEpisodeCount(): int;

    /**
     * Get the total episode count.
     *
     * @return int
     */
    public function getTotalEpisodeCount(): int;

    /**
     * Get the size on disk.
     *
     * @return int
     */
    public function getSizeOnDisk(): int;

    /**
     * Get the release groups.
     *
     * @return array<string>
     */
    public function getReleaseGroups(): array;

    /**
     * Get the percent of episodes.
     *
     * @return float
     */
    public function getPercentOfEpisodes(): float;
}
