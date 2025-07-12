<?php
// AI GENERATED

namespace App\Interfaces\DiskUsage;

interface DiskUsageInterface
{
    /**
     * Get the disk ID.
     *
     * @return int
     */
    public function getId(): int;

    /**
     * Get the disk path.
     *
     * @return string
     */
    public function getPath(): string;

    /**
     * Get the disk label.
     *
     * @return string
     */
    public function getLabel(): string;

    /**
     * Get the free space on the disk.
     *
     * @return int
     */
    public function getFreeSpace(): int;

    /**
     * Get the total space on the disk.
     *
     * @return int
     */
    public function getTotalSpace(): int;
}
