<?php

declare(strict_types=1);

namespace DoctrineORMModule\Options;

use Laminas\Stdlib\AbstractOptions;

/**
 * Configuration options for an collector
 */
class SQLLoggerCollectorOptions extends AbstractOptions
{
    /** @var string name to be assigned to the collector */
    protected $name = 'orm_default';

    /** @var string|null service name of the configuration where the logger has to be put */
    protected $configuration;

    /** @var string|null service name of the SQLLogger to be used */
    protected $sqlLogger;

    public function setName(?string $name): void
    {
        $this->name = (string) $name;
    }

    /**
     * Name of the collector
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function setConfiguration(?string $configuration): void
    {
        $this->configuration = $configuration ? (string) $configuration : null;
    }

    /**
     * Configuration service name (where to set the logger)
     */
    public function getConfiguration(): string
    {
        return $this->configuration ? $this->configuration : 'doctrine.configuration.orm_default';
    }

    public function setSqlLogger(?string $sqlLogger): void
    {
        $this->sqlLogger = $sqlLogger ? (string) $sqlLogger : null;
    }

    /**
     * SQLLogger service name
     */
    public function getSqlLogger(): ?string
    {
        return $this->sqlLogger;
    }
}
