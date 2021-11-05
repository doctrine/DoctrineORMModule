<?php

declare(strict_types=1);

namespace DoctrineORMModule\Options;

use Laminas\Stdlib\AbstractOptions;

/**
 * Configuration options for a DBAL Connection
 */
class DBALConfiguration extends AbstractOptions
{
    /**
     * Set the cache key for the result cache. Cache key
     * is assembled as "doctrine.cache.{key}" and pulled from
     * service locator.
     *
     * @var string
     */
    protected $resultCache = 'array';

    /**
     * Set the class name of the SQL Logger, or null, to disable.
     *
     * @var string
     */
    protected $sqlLogger = null;

    /**
     * Keys must be the name of the type identifier and value is
     * the class name of the Type
     *
     * @var mixed[]
     */
    protected $types = [];

    public function setResultCache(string $resultCache): void
    {
        $this->resultCache = $resultCache;
    }

    public function getResultCache(): string
    {
        return 'doctrine.cache.' . $this->resultCache;
    }

    public function setSqlLogger(string $sqlLogger): void
    {
        $this->sqlLogger = $sqlLogger;
    }

    public function getSqlLogger(): ?string
    {
        return $this->sqlLogger;
    }

    /**
     * @param mixed[] $types
     */
    public function setTypes(array $types): void
    {
        $this->types = $types;
    }

    /**
     * @return mixed
     */
    public function getTypes()
    {
        return $this->types;
    }
}
