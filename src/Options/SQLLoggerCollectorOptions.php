<?php

declare(strict_types=1);

namespace DoctrineORMModule\Options;

use Laminas\Stdlib\AbstractOptions;

/**
 * Configuration options for a collector
 *
 * @template-extends AbstractOptions<mixed>
 */
final class SQLLoggerCollectorOptions extends AbstractOptions
{
    /** @var string name to be assigned to the collector */
    protected string $name = 'orm_default';

    /** @var string|null service name of the configuration where the logger has to be put */
    protected ?string $configuration = null;

    /** @var string|null service name of the SQLLogger to be used */
    protected ?string $sqlLogger = null;

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
        $this->configuration = $configuration ?: null;
    }

    /**
     * Configuration service name (where to set the logger)
     */
    public function getConfiguration(): string
    {
        return $this->configuration ?: 'doctrine.configuration.orm_default';
    }

    public function setSqlLogger(?string $sqlLogger): void
    {
        $this->sqlLogger = $sqlLogger ?: null;
    }

    /**
     * SQLLogger service name
     */
    public function getSqlLogger(): ?string
    {
        return $this->sqlLogger;
    }
}
