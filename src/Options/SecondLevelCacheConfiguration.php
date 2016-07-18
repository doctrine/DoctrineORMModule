<?php

namespace DoctrineORMModule\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Configuration options for Second Level Cache
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Michaël Gallego <mic.gallego@gmail.com>
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
     * ['My\Region' => ['lifetime' => 200, 'lock_lifetime' => 400]]
     *
     * @var array
     */
    protected $regions = [];

    /**
     * @param  bool $enabled
     * @return $this
     */
    public function setEnabled($enabled)
    {
        $this->enabled = (bool) $enabled;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param  int $defaultLifetime
     * @return $this
     */
    public function setDefaultLifetime($defaultLifetime)
    {
        $this->defaultLifetime = (int) $defaultLifetime;

        return $this;
    }

    /**
     * @return int
     */
    public function getDefaultLifetime()
    {
        return $this->defaultLifetime;
    }

    /**
     * @param  int $defaultLockLifetime
     * @return $this
     */
    public function setDefaultLockLifetime($defaultLockLifetime)
    {
        $this->defaultLockLifetime = (int) $defaultLockLifetime;

        return $this;
    }

    /**
     * @return int
     */
    public function getDefaultLockLifetime()
    {
        return $this->defaultLockLifetime;
    }

    /**
     * @param  string $fileLockRegionDirectory
     * @return $this
     */
    public function setFileLockRegionDirectory($fileLockRegionDirectory)
    {
        $this->fileLockRegionDirectory = (string) $fileLockRegionDirectory;

        return $this;
    }

    /**
     * @return string
     */
    public function getFileLockRegionDirectory()
    {
        return $this->fileLockRegionDirectory;
    }

    /**
     * @param  array $regions
     * @return $this
     */
    public function setRegions(array $regions)
    {
        $this->regions = $regions;

        return $this;
    }

    /**
     * @return array
     */
    public function getRegions()
    {
        return $this->regions;
    }
}
