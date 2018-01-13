<?php

namespace DoctrineORMModule\Collector;

use ZendDeveloperTools\Collector\CollectorInterface;
use ZendDeveloperTools\Collector\AutoHideInterface;

use Zend\Mvc\MvcEvent;

use Doctrine\DBAL\Logging\DebugStack;

/**
 * Collector to be used in ZendDeveloperTools to record and display SQL queries
 *
 * @license MIT
 * @link    www.doctrine-project.org
 * @author  Marco Pivetta <ocramius@gmail.com>
 */
class SQLLoggerCollector implements CollectorInterface, AutoHideInterface
{
    /**
     * Collector priority
     */
    const PRIORITY = 10;

    /**
     * @var DebugStack
     */
    protected $sqlLogger;

    /**
     * @var string
     */
    protected $name;

    /**
     * @param DebugStack $sqlLogger
     * @param string     $name
     */
    public function __construct(DebugStack $sqlLogger, $name)
    {
        $this->sqlLogger = $sqlLogger;
        $this->name = (string) $name;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getPriority()
    {
        return static::PRIORITY;
    }

    /**
     * {@inheritDoc}
     */
    public function collect(MvcEvent $mvcEvent)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function canHide()
    {
        return empty($this->sqlLogger->queries);
    }

    /**
     * @return int
     */
    public function getQueryCount()
    {
        return count($this->sqlLogger->queries);
    }

    /**
     * @return array
     */
    public function getQueries()
    {
        return $this->sqlLogger->queries;
    }

    /**
     * @return float
     */
    public function getQueryTime()
    {
        $time = 0.0;

        foreach ($this->sqlLogger->queries as $query) {
            $time += $query['executionMS'];
        }

        return $time;
    }
}
