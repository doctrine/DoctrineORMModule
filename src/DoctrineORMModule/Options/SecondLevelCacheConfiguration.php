<?php

declare(strict_types=1);

namespace DoctrineORMModule\Options;

use Laminas\Stdlib\AbstractOptions;

/**
 * Configuration options for Second Level Cache
 */
class SecondLevelCacheConfiguration extends AbstractOptions
{
    /**
     * Enable the second level cache configuration
     *
     * @var bool
     */
    protected $enabled = false;

    /**
     * Default lifetime
     *
     * @var int
     */
    protected $defaultLifetime = 3600;

    /**
     * Default lock lifetime
     *
     * @var int
     */
    protected $defaultLockLifetime = 60;

    /**
     * The file lock region directory (needed for some cache usage)
     *
     * @var string
     */
    protected $fileLockRegionDirectory = '';

    /**
     * Configure the lifetime and lock lifetime per region. You must pass an associative array like this:
     *
     * [
     *     'My\Region' => ['lifetime' => 200, 'lock_lifetime' => 400],
     * ]
     *
     * @var mixed[]
     */
    protected $regions = [];

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = (bool) $enabled;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setDefaultLifetime(int $defaultLifetime): void
    {
        $this->defaultLifetime = (int) $defaultLifetime;
    }

    public function getDefaultLifetime(): int
    {
        return $this->defaultLifetime;
    }

    public function setDefaultLockLifetime(int $defaultLockLifetime): void
    {
        $this->defaultLockLifetime = (int) $defaultLockLifetime;
    }

    public function getDefaultLockLifetime(): int
    {
        return $this->defaultLockLifetime;
    }

    public function setFileLockRegionDirectory(string $fileLockRegionDirectory): void
    {
        $this->fileLockRegionDirectory = (string) $fileLockRegionDirectory;
    }

    public function getFileLockRegionDirectory(): string
    {
        return $this->fileLockRegionDirectory;
    }

    /**
     * @param mixed[] $regions
     */
    public function setRegions(array $regions): void
    {
        $this->regions = $regions;
    }

    /**
     * @return mixed[]
     */
    public function getRegions(): array
    {
        return $this->regions;
    }
}
