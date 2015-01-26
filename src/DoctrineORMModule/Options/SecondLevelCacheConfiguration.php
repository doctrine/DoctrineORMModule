<?php

namespace DoctrineORMModule\Options;

use DoctrineORMModule\Options\DBALConfiguration;
use Doctrine\ORM\Mapping\EntityListenerResolver;
use Doctrine\ORM\Mapping\NamingStrategy;
use Doctrine\ORM\Repository\RepositoryFactory;
use Zend\Stdlib\AbstractOptions;
use Zend\Stdlib\Exception\InvalidArgumentException;

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
     * array(
     *     'My\Region' => array('lifetime' => 200, 'lock_lifetime' => 400)
     * )
     *
     * @var array
     */
    protected $regions = array();

    /**
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = (bool) $enabled;
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param int $defaultLifetime
     */
    public function setDefaultLifetime($defaultLifetime)
    {
        $this->defaultLifetime = (int) $defaultLifetime;
    }

    /**
     * @return int
     */
    public function getDefaultLifetime()
    {
        return $this->defaultLifetime;
    }

    /**
     * @param int $defaultLockLifetime
     */
    public function setDefaultLockLifetime($defaultLockLifetime)
    {
        $this->defaultLockLifetime = (int) $defaultLockLifetime;
    }

    /**
     * @return int
     */
    public function getDefaultLockLifetime()
    {
        return $this->defaultLockLifetime;
    }

    /**
     * @param string $fileLockRegionDirectory
     */
    public function setFileLockRegionDirectory($fileLockRegionDirectory)
    {
        $this->fileLockRegionDirectory = (string) $fileLockRegionDirectory;
    }

    /**
     * @return string
     */
    public function getFileLockRegionDirectory()
    {
        return $this->fileLockRegionDirectory;
    }

    /**
     * @param array $regions
     */
    public function setRegions(array $regions)
    {
        $this->regions = $regions;
    }

    /**
     * @return array
     */
    public function getRegions()
    {
        return $this->regions;
    }
}
