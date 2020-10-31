<?php

declare(strict_types=1);

namespace DoctrineORMModule\Collector;

use Doctrine\DBAL\Logging\DebugStack;
use Laminas\DeveloperTools\Collector\AutoHideInterface;
use Laminas\DeveloperTools\Collector\CollectorInterface;
use Laminas\Mvc\MvcEvent;

use function count;

/**
 * Collector to be used in DeveloperTools to record and display SQL queries
 */
class SQLLoggerCollector implements CollectorInterface, AutoHideInterface
{
    /**
     * Collector priority
     */
    public const PRIORITY = 10;

    /** @var DebugStack */
    protected $sqlLogger;

    /** @var string */
    protected $name;

    public function __construct(DebugStack $sqlLogger, string $name)
    {
        $this->sqlLogger = $sqlLogger;
        $this->name      = (string) $name;
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
        return self::PRIORITY;
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

    public function getQueryCount(): int
    {
        return count($this->sqlLogger->queries);
    }

    /**
     * @return mixed[]
     */
    public function getQueries(): array
    {
        return $this->sqlLogger->queries;
    }

    public function getQueryTime(): float
    {
        $time = 0.0;

        foreach ($this->sqlLogger->queries as $query) {
            $time += $query['executionMS'];
        }

        return $time;
    }
}
