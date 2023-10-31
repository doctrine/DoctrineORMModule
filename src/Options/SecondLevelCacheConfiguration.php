<?php

declare(strict_types=1);

namespace DoctrineORMModule\Options;

use Laminas\Stdlib\AbstractOptions;

/**
 * Configuration options for Second Level Cache
 *
 * @template-extends AbstractOptions<mixed>
 */
final class SecondLevelCacheConfiguration extends AbstractOptions
{
    /**
     * Enable the second level cache configuration
     */
    protected bool $enabled = false;

    /**
     * Default lifetime
     */
    protected int $defaultLifetime = 3600;

    /**
     * Default lock lifetime
     */
    protected int $defaultLockLifetime = 60;

    /**
     * The file lock region directory (needed for some cache usage)
     */
    protected string $fileLockRegionDirectory = '';

    /**
     * Configure the lifetime and lock lifetime per region. You must pass an associative array like this:
     *
     * [
     *     'My\Region' => ['lifetime' => 200, 'lock_lifetime' => 400],
     * ]
     *
     * @var mixed[]
     */
    protected array $regions = [];

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setDefaultLifetime(int $defaultLifetime): void
    {
        $this->defaultLifetime = $defaultLifetime;
    }

    public function getDefaultLifetime(): int
    {
        return $this->defaultLifetime;
    }

    public function setDefaultLockLifetime(int $defaultLockLifetime): void
    {
        $this->defaultLockLifetime = $defaultLockLifetime;
    }

    public function getDefaultLockLifetime(): int
    {
        return $this->defaultLockLifetime;
    }

    public function setFileLockRegionDirectory(string $fileLockRegionDirectory): void
    {
        $this->fileLockRegionDirectory = $fileLockRegionDirectory;
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
