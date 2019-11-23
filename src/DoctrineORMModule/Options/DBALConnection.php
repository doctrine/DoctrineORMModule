<?php

namespace DoctrineORMModule\Options;

use Doctrine\DBAL\Driver\PDOMySql\Driver;
use Zend\Stdlib\AbstractOptions;

/**
 * DBAL Connection options
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Kyle Spraggs <theman@spiffyjr.me>
 */
class DBALConnection extends AbstractOptions
{
    /**
     * Set the configuration key for the Configuration. Configuration key
     * is assembled as "doctrine.configuration.{key}" and pulled from
     * service locator.
     *
     * @var string
     */
    protected $configuration = 'orm_default';

    /**
     * Set the eventmanager key for the EventManager. EventManager key
     * is assembled as "doctrine.eventmanager.{key}" and pulled from
     * service locator.
     *
     * @var string
     */
    protected $eventmanager = 'orm_default';

    /**
     * Set the PDO instance, if any, to use. If a string is set
     * then the alias is pulled from the service locator.
     *
     * @var null|string|\PDO
     */
    protected $pdo = null;

    /**
     * Setting the driver is deprecated. You should set the
     * driver class directly instead.
     *
     * @var string
     */
    protected $driverClass = Driver::class;

    /**
     * Set the wrapper class for the driver. In general, this should not
     * need to be changed.
     *
     * @var string|null
     */
    protected $wrapperClass = null;

    /**
     * Driver specific connection parameters.
     *
     * @var array
     */
    protected $params = [];

    /**
     * @var array
     */
    protected $doctrineTypeMappings = [];

    /**
     * @var array
     */
    protected $doctrineCommentedTypes = [];

    /**
     * @var bool
     */
    protected $useSavepoints = false;

    /**
     * @param string $configuration
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return string
     */
    public function getConfiguration()
    {
        return "doctrine.configuration.{$this->configuration}";
    }

    /**
     * @param string $eventmanager
     */
    public function setEventmanager($eventmanager)
    {
        $this->eventmanager = $eventmanager;
    }

    /**
     * @return string
     */
    public function getEventmanager()
    {
        return "doctrine.eventmanager.{$this->eventmanager}";
    }

    /**
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param  array                                     $doctrineTypeMappings
     * @return \DoctrineORMModule\Options\DBALConnection
     */
    public function setDoctrineTypeMappings($doctrineTypeMappings)
    {
        $this->doctrineTypeMappings = (array) $doctrineTypeMappings;

        return $this;
    }

    /**
     *
     * @return array
     */
    public function getDoctrineTypeMappings()
    {
        return $this->doctrineTypeMappings;
    }

    /**
     * @param  array                                     $doctrineCommentedTypes
     */
    public function setDoctrineCommentedTypes(array $doctrineCommentedTypes)
    {
        $this->doctrineCommentedTypes = $doctrineCommentedTypes;
    }

    /**
     * @return array
     */
    public function getDoctrineCommentedTypes()
    {
        return $this->doctrineCommentedTypes;
    }

    /**
     * @param null|string $driverClass
     */
    public function setDriverClass($driverClass)
    {
        $this->driverClass = $driverClass;
    }

    /**
     * @return null|string
     */
    public function getDriverClass()
    {
        return $this->driverClass;
    }

    /**
     * @param null|\PDO|string $pdo
     */
    public function setPdo($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return null|\PDO|string
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * @param string $wrapperClass
     */
    public function setWrapperClass($wrapperClass)
    {
        $this->wrapperClass = $wrapperClass;
    }

    /**
     * @return string
     */
    public function getWrapperClass()
    {
        return $this->wrapperClass;
    }

    /**
     * @return bool
     */
    public function useSavepoints()
    {
        return $this->useSavepoints;
    }

    /**
     * @param bool $useSavepoints
     */
    public function setUseSavepoints($useSavepoints)
    {
        $this->useSavepoints = $useSavepoints;
    }
}
