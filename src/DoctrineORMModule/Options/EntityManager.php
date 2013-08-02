<?php

namespace DoctrineORMModule\Options;

use Zend\Stdlib\AbstractOptions;

class EntityManager extends AbstractOptions
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
     * Set the connection key for the Connection. Connection key
     * is assembled as "doctrine.connection.{key}" and pulled from
     * service locator.
     *
     * @var string
     */
    protected $connection = 'orm_default';

    /**
     * Set the connection key for the EntityResolver, which is
     * a service of type {@see \Doctrine\ORM\Tools\ResolveTargetEntityListener}.
     * The EntityResolver service name is assembled
     * as "doctrine.entity_resolver.{key}"
     *
     * @var string
     */
    protected $entityResolver = 'orm_default';

    /**
     * @param  string $configuration
     * @return self
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;

        return $this;
    }

    /**
     * @return string
     */
    public function getConfiguration()
    {
        return "doctrine.configuration.{$this->configuration}";
    }

    /**
     * @param  string $connection
     * @return self
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * @return string
     * @return self
     */
    public function getConnection()
    {
        return 'doctrine.connection.' . $this->connection;
    }

    /**
     * @param  string $entityResolver
     * @return self
     */
    public function setEntityResolver($entityResolver)
    {
        $this->entityResolver = (string) $entityResolver;

        return $this;
    }

    /**
     * @return string
     * @return self
     */
    public function getEntityResolver()
    {
        return 'doctrine.entity_resolver.' . $this->entityResolver;
    }
}
