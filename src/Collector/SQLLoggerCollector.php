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

    protected DebugStack $sqlLogger;

    protected string $name;

    public function __construct(DebugStack $sqlLogger, string $name)
    {
        $this->sqlLogger = $sqlLogger;
        $this->name      = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPriority(): int
    {
        return self::PRIORITY;
    }

    public function collect(MvcEvent $mvcEvent): void
    {
    }

    public function canHide(): bool
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
