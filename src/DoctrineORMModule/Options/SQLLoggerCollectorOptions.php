<?php

namespace DoctrineORMModule\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Configuration options for an collector
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Marco Pivetta <ocramius@gmail.com>
 */
class SQLLoggerCollectorOptions extends AbstractOptions
{
    /**
     * @var string name to be assigned to the collector
     */
    protected $name = 'orm_default';

    /**
     * @var string|null service name of the configuration where the logger has to be put
     */
    protected $configuration;

    /**
     * @var string|null service name of the SQLLogger to be used
     */
    protected $sqlLogger;

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = (string) $name;
    }

    /**
     * Name of the collector
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string|null $configuration
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration ? (string) $configuration : null;
    }

    /**
     * Configuration service name (where to set the logger)
     *
     * @return string
     */
    public function getConfiguration()
    {
        return $this->configuration ? $this->configuration : 'doctrine.configuration.orm_default';
    }

    /**
     * @param string|null $sqlLogger
     */
    public function setSqlLogger($sqlLogger)
    {
        $this->sqlLogger = $sqlLogger ? (string) $sqlLogger : null;
    }

    /**
     * SQLLogger service name
     *
     * @return string|null
     */
    public function getSqlLogger()
    {
        return $this->sqlLogger;
    }
}
