<?php
// AI GENERATED

namespace App\Interfaces\Series;

interface AddOptionsInterface
{
    /**
     * Check if episodes with files should be ignored.
     *
     * @return bool
     */
    public function ignoreEpisodesWithFiles(): bool;

    /**
     * Check if episodes without files should be ignored.
     *
     * @return bool
     */
    public function ignoreEpisodesWithoutFiles(): bool;

    /**
     * Get the monitor setting.
     *
     * @return string
     */
    public function getMonitor(): string;

    /**
     * Check if missing episodes should be searched for.
     *
     * @return bool
     */
    public function searchForMissingEpisodes(): bool;

    /**
     * Check if cutoff unmet episodes should be searched for.
     *
     * @return bool
     */
    public function searchForCutoffUnmetEpisodes(): bool;
}
