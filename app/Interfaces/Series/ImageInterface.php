<?php
// AI GENERATED

namespace App\Interfaces\Series;

interface ImageInterface
{
    /**
     * Get the cover type.
     *
     * @return string
     */
    public function getCoverType(): string;

    /**
     * Get the URL.
     *
     * @return string
     */
    public function getUrl(): string;

    /**
     * Get the remote URL.
     *
     * @return string|null
     */
    public function getRemoteUrl(): ?string;
}
