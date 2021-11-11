<?php

declare(strict_types=1);

namespace DoctrineORMModule\Options;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\PDO\MySQL\Driver as PDOMySQLDriver;
use Laminas\Stdlib\AbstractOptions;
use PDO;

/**
 * DBAL Connection options
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
     * @var string|PDO|null
     */
    protected $pdo = null;

    /**
     * Setting the driver is deprecated. You should set the
     * driver class directly instead.
     *
     * @var string
     */
    protected $driverClass = PDOMySQLDriver::class;

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
     * @var mixed[]
     */
    protected $params = [];

    /** @var mixed[] */
    protected $doctrineTypeMappings = [];

    /** @var mixed[] */
    protected $doctrineCommentedTypes = [];

    /** @var bool */
    protected $useSavepoints = false;

    public function setConfiguration(string $configuration): void
    {
        $this->configuration = $configuration;
    }

    public function getConfiguration(): string
    {
        return 'doctrine.configuration.' . $this->configuration;
    }

    public function setEventmanager(string $eventmanager): void
    {
        $this->eventmanager = $eventmanager;
    }

    public function getEventmanager(): string
    {
        return 'doctrine.eventmanager.' . $this->eventmanager;
    }

    /**
     * @param mixed[] $params
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    /**
     * @return mixed[]
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param mixed[] $doctrineTypeMappings
     */
    public function setDoctrineTypeMappings(array $doctrineTypeMappings): DBALConnection
    {
        $this->doctrineTypeMappings = (array) $doctrineTypeMappings;

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function getDoctrineTypeMappings(): array
    {
        return $this->doctrineTypeMappings;
    }

    /**
     * @param mixed[] $doctrineCommentedTypes
     */
    public function setDoctrineCommentedTypes(array $doctrineCommentedTypes): void
    {
        $this->doctrineCommentedTypes = $doctrineCommentedTypes;
    }

    /**
     * @return mixed[]
     */
    public function getDoctrineCommentedTypes(): array
    {
        return $this->doctrineCommentedTypes;
    }

    public function setDriverClass(?string $driverClass): void
    {
        $this->driverClass = $driverClass;
    }

    /**
     * @return class-string<Driver>|null
     */
    public function getDriverClass(): ?string
    {
        return $this->driverClass;
    }

    /**
     * @param PDO|string|null $pdo
     */
    public function setPdo($pdo): void
    {
        $this->pdo = $pdo;
    }

    /**
     * @return PDO|string|null
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    public function setWrapperClass(string $wrapperClass): void
    {
        $this->wrapperClass = $wrapperClass;
    }

    /**
     * @return class-string<Connection>|null
     */
    public function getWrapperClass(): ?string
    {
        return $this->wrapperClass;
    }

    public function useSavepoints(): bool
    {
        return $this->useSavepoints;
    }

    public function setUseSavepoints(bool $useSavepoints): void
    {
        $this->useSavepoints = $useSavepoints;
    }
}
