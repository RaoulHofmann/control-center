<?php
// AI GENERATED

namespace App\Interfaces\Series;

interface SeriesInterface
{
    /**
     * Get the series ID.
     *
     * @return int
     */
    public function getId(): int;

    /**
     * Get the series title.
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Get the alternate titles for the series.
     *
     * @return array<AlternateTitleInterface>
     */
    public function getAlternateTitles(): array;

    /**
     * Get the sort title.
     *
     * @return string
     */
    public function getSortTitle(): string;

    /**
     * Get the status of the series.
     *
     * @return string
     */
    public function getStatus(): string;

    /**
     * Check if the series has ended.
     *
     * @return bool
     */
    public function isEnded(): bool;

    /**
     * Get the profile name.
     *
     * @return string
     */
    public function getProfileName(): string;

    /**
     * Get the overview of the series.
     *
     * @return string
     */
    public function getOverview(): string;

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
     * Get the network.
     *
     * @return string
     */
    public function getNetwork(): string;

    /**
     * Get the air time.
     *
     * @return string
     */
    public function getAirTime(): string;

    /**
     * Get the images for the series.
     *
     * @return array<ImageInterface>
     */
    public function getImages(): array;

    /**
     * Get the original language.
     *
     * @return LanguageInterface
     */
    public function getOriginalLanguage(): LanguageInterface;

    /**
     * Get the remote poster URL.
     *
     * @return string
     */
    public function getRemotePoster(): string;

    /**
     * Get the seasons of the series.
     *
     * @return array<SeasonInterface>
     */
    public function getSeasons(): array;

    /**
     * Get the year of the series.
     *
     * @return int
     */
    public function getYear(): int;

    /**
     * Get the path of the series.
     *
     * @return string
     */
    public function getPath(): string;

    /**
     * Get the quality profile ID.
     *
     * @return int
     */
    public function getQualityProfileId(): int;

    /**
     * Check if season folder is used.
     *
     * @return bool
     */
    public function useSeasonFolder(): bool;

    /**
     * Check if the series is monitored.
     *
     * @return bool
     */
    public function isMonitored(): bool;

    /**
     * Get the monitor new items setting.
     *
     * @return string
     */
    public function getMonitorNewItems(): string;

    /**
     * Check if scene numbering is used.
     *
     * @return bool
     */
    public function useSceneNumbering(): bool;

    /**
     * Get the runtime of episodes.
     *
     * @return int
     */
    public function getRuntime(): int;

    /**
     * Get the TVDB ID.
     *
     * @return int
     */
    public function getTvdbId(): int;

    /**
     * Get the TV Rage ID.
     *
     * @return int
     */
    public function getTvRageId(): int;

    /**
     * Get the TV Maze ID.
     *
     * @return int
     */
    public function getTvMazeId(): int;

    /**
     * Get the TMDB ID.
     *
     * @return int
     */
    public function getTmdbId(): int;

    /**
     * Get the first aired date.
     *
     * @return string|null
     */
    public function getFirstAired(): ?string;

    /**
     * Get the last aired date.
     *
     * @return string|null
     */
    public function getLastAired(): ?string;

    /**
     * Get the series type.
     *
     * @return string
     */
    public function getSeriesType(): string;

    /**
     * Get the clean title.
     *
     * @return string
     */
    public function getCleanTitle(): string;

    /**
     * Get the IMDB ID.
     *
     * @return string
     */
    public function getImdbId(): string;

    /**
     * Get the title slug.
     *
     * @return string
     */
    public function getTitleSlug(): string;

    /**
     * Get the root folder path.
     *
     * @return string
     */
    public function getRootFolderPath(): string;

    /**
     * Get the folder name.
     *
     * @return string
     */
    public function getFolder(): string;

    /**
     * Get the certification.
     *
     * @return string
     */
    public function getCertification(): string;

    /**
     * Get the genres.
     *
     * @return array<string>
     */
    public function getGenres(): array;

    /**
     * Get the tags.
     *
     * @return array<int>
     */
    public function getTags(): array;

    /**
     * Get the date when the series was added.
     *
     * @return string
     */
    public function getAdded(): string;

    /**
     * Get the add options.
     *
     * @return AddOptionsInterface
     */
    public function getAddOptions(): AddOptionsInterface;

    /**
     * Get the ratings.
     *
     * @return RatingsInterface
     */
    public function getRatings(): RatingsInterface;

    /**
     * Get the statistics.
     *
     * @return StatisticsInterface
     */
    public function getStatistics(): StatisticsInterface;

    /**
     * Check if episodes have changed.
     *
     * @return bool
     */
    public function hasEpisodesChanged(): bool;
}
