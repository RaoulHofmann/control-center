<?php
// AI GENERATED

namespace App\Interfaces\Series;

interface AlternateTitleInterface
{
    /**
     * Get the alternate title.
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Get the season number.
     *
     * @return int|null
     */
    public function getSeasonNumber(): ?int;

    /**
     * Get the scene season number.
     *
     * @return int|null
     */
    public function getSceneSeasonNumber(): ?int;

    /**
     * Get the scene origin.
     *
     * @return string|null
     */
    public function getSceneOrigin(): ?string;

    /**
     * Get the comment.
     *
     * @return string|null
     */
    public function getComment(): ?string;
}
