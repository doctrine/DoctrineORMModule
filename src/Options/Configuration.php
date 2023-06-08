<?php

declare(strict_types=1);

namespace DoctrineORMModule\Options;

use Doctrine\DBAL\Driver\Middleware;
use Doctrine\ORM\Mapping\EntityListenerResolver;
use Doctrine\ORM\Mapping\NamingStrategy;
use Doctrine\ORM\Mapping\QuoteStrategy;
use Doctrine\ORM\Repository\RepositoryFactory;

/**
 * Configuration options for an ORM Configuration
 */
final class Configuration extends DBALConfiguration
{
    /**
     * Set the cache key for the metadata cache. Cache key
     * is assembled as "doctrine.cache.{key}" and pulled from
     * service locator.
     */
    protected string $metadataCache = 'array';

    /**
     * Set the cache key for the query cache. Cache key
     * is assembled as "doctrine.cache.{key}" and pulled from
     * service locator.
     */
    protected string $queryCache = 'array';

    /**
     * Set the cache key for the result cache. Cache key
     * is assembled as "doctrine.cache.{key}" and pulled from
     * service locator.
     */
    protected string $resultCache = 'array';

    /**
     * Set the cache key for the hydration cache. Cache key
     * is assembled as "doctrine.cache.{key}" and pulled from
     * service locator.
     */
    protected string $hydrationCache = 'array';

    /**
     * Set the driver key for the metadata driver. Driver key
     * is assembled as "doctrine.driver.{key}" and pulled from
     * service locator.
     */
    protected string $driver = 'orm_default';

    /**
     * Automatic generation of proxies (disable for production!)
     */
    protected bool $generateProxies = true;

    /**
     * Proxy directory.
     */
    protected string $proxyDir = 'data';

    /**
     * Proxy namespace.
     */
    protected string $proxyNamespace = 'DoctrineORMModule\Proxy';

    /**
     * Entity alias map.
     *
     * @var mixed[]
     */
    protected array $entityNamespaces = [];

    /**
     * Keys must be function names and values the FQCN of the implementing class.
     * The function names will be case-insensitive in DQL.
     *
     * @var mixed[]
     */
    protected array $datetimeFunctions = [];

    /**
     * Keys must be function names and values the FQCN of the implementing class.
     * The function names will be case-insensitive in DQL.
     *
     * @var mixed[]
     */
    protected array $stringFunctions = [];

    /**
     * Keys must be function names and values the FQCN of the implementing class.
     * The function names will be case-insensitive in DQL.
     *
     * @var mixed[]
     */
    protected array $numericFunctions = [];

    /**
     * Keys must be the name of the custom filter and the value must be
     * the class name for the custom filter.
     *
     * @var mixed[]
     */
    protected array $filters = [];

    /**
     * Keys must be the name of the query and values the DQL query string.
     *
     * @var mixed[]
     */
    protected array $namedQueries = [];

    /**
     * Keys must be the name of the query and the value is an array containing
     * the keys 'sql' for native SQL query string and 'rsm' for the Query\ResultSetMapping.
     *
     * @var mixed[]
     */
    protected array $namedNativeQueries = [];

    /**
     * Keys must be the name of the custom hydration method and the value must be
     * the class name for the custom hydrator
     *
     * @var mixed[]
     */
    protected array $customHydrationModes = [];

    /**
     * Naming strategy or name of the naming strategy service to be set in ORM
     * configuration (if any)
     */
    protected string|NamingStrategy|null $namingStrategy = null;

    /**
     * Quote strategy or name of the quote strategy service to be set in ORM
     * configuration (if any)
     */
    protected string|QuoteStrategy|null $quoteStrategy = null;

    /**
     * Default repository class
     */
    protected ?string $defaultRepositoryClassName = null;

    /**
     * Repository factory or name of the repository factory service to be set in ORM
     * configuration (if any)
     */
    protected string|RepositoryFactory|null $repositoryFactory = null;

    /**
     * Class name of MetaData factory to be set in ORM.
     * The entityManager will create a new instance on construction.
     */
    protected ?string $classMetadataFactoryName = null;

    /**
     * Entity listener resolver or service name of the entity listener resolver
     * to be set in ORM configuration (if any)
     *
     * @link http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/events.html
     */
    protected string|EntityListenerResolver|null $entityListenerResolver = null;

    /**
     * Configuration for second level cache
     *
     * @link http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/second-level-cache.html
     */
    protected ?SecondLevelCacheConfiguration $secondLevelCache = null;

    /**
     * Configuration option for the filter schema assets expression
     */
    protected ?string $filterSchemaAssetsExpression = null;

    /**
     * Stack of middleware names
     *
     * @var array<class-string<Middleware>>
     */
    protected array $middlewares = [];

    /**
     * Configuration option for the schema assets filter callable
     *
     * @var callable|null
     */
    protected $schemaAssetsFilter = null;

    /**
     * @param mixed[] $datetimeFunctions
     */
    public function setDatetimeFunctions(array $datetimeFunctions): self
    {
        $this->datetimeFunctions = $datetimeFunctions;

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function getDatetimeFunctions(): array
    {
        return $this->datetimeFunctions;
    }

    public function setDriver(string $driver): self
    {
        $this->driver = $driver;

        return $this;
    }

    public function getDriver(): string
    {
        return 'doctrine.driver.' . $this->driver;
    }

    /**
     * @param mixed[] $entityNamespaces
     */
    public function setEntityNamespaces(array $entityNamespaces): self
    {
        $this->entityNamespaces = $entityNamespaces;

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function getEntityNamespaces(): array
    {
        return $this->entityNamespaces;
    }

    public function setGenerateProxies(bool $generateProxies): self
    {
        $this->generateProxies = $generateProxies;

        return $this;
    }

    public function getGenerateProxies(): bool
    {
        return $this->generateProxies;
    }

    public function setMetadataCache(string $metadataCache): self
    {
        $this->metadataCache = $metadataCache;

        return $this;
    }

    public function getMetadataCache(): string
    {
        return 'doctrine.cache.' . $this->metadataCache;
    }

    public function setResultCache(string $resultCache): void
    {
        $this->resultCache = $resultCache;
    }

    public function getResultCache(): string
    {
        return 'doctrine.cache.' . $this->resultCache;
    }

    public function setHydrationCache(string $hydrationCache): self
    {
        $this->hydrationCache = $hydrationCache;

        return $this;
    }

    public function getHydrationCache(): string
    {
        return 'doctrine.cache.' . $this->hydrationCache;
    }

    /**
     * @param mixed[] $namedNativeQueries
     */
    public function setNamedNativeQueries(array $namedNativeQueries): self
    {
        $this->namedNativeQueries = $namedNativeQueries;

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function getNamedNativeQueries(): array
    {
        return $this->namedNativeQueries;
    }

    /**
     * @param mixed[] $namedQueries
     */
    public function setNamedQueries(array $namedQueries): self
    {
        $this->namedQueries = $namedQueries;

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function getNamedQueries(): array
    {
        return $this->namedQueries;
    }

    /**
     * @param  mixed[] $numericFunctions
     */
    public function setNumericFunctions(array $numericFunctions): self
    {
        $this->numericFunctions = $numericFunctions;

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function getNumericFunctions(): array
    {
        return $this->numericFunctions;
    }

    /**
     * @param mixed[] $filters
     */
    public function setFilters(array $filters): self
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    public function setProxyDir(string $proxyDir): self
    {
        $this->proxyDir = $proxyDir;

        return $this;
    }

    public function getProxyDir(): string
    {
        return $this->proxyDir;
    }

    public function setProxyNamespace(string $proxyNamespace): self
    {
        $this->proxyNamespace = $proxyNamespace;

        return $this;
    }

    public function getProxyNamespace(): string
    {
        return $this->proxyNamespace;
    }

    public function setQueryCache(string $queryCache): self
    {
        $this->queryCache = $queryCache;

        return $this;
    }

    public function getQueryCache(): string
    {
        return 'doctrine.cache.' . $this->queryCache;
    }

    /**
     * @param  mixed[] $stringFunctions
     */
    public function setStringFunctions(array $stringFunctions): self
    {
        $this->stringFunctions = $stringFunctions;

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function getStringFunctions(): array
    {
        return $this->stringFunctions;
    }

    /**
     * @param mixed[] $modes
     */
    public function setCustomHydrationModes(array $modes): self
    {
        $this->customHydrationModes = $modes;

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function getCustomHydrationModes(): array
    {
        return $this->customHydrationModes;
    }

    public function setNamingStrategy(string|NamingStrategy|null $namingStrategy): self
    {
        $this->namingStrategy = $namingStrategy;

        return $this;
    }

    public function getNamingStrategy(): string|NamingStrategy|null
    {
        return $this->namingStrategy;
    }

    public function setQuoteStrategy(string|QuoteStrategy|null $quoteStrategy): self
    {
        $this->quoteStrategy = $quoteStrategy;

        return $this;
    }

    public function getQuoteStrategy(): string|QuoteStrategy|null
    {
        return $this->quoteStrategy;
    }

    public function setRepositoryFactory(string|RepositoryFactory|null $repositoryFactory): self
    {
        $this->repositoryFactory = $repositoryFactory;

        return $this;
    }

    public function getRepositoryFactory(): string|RepositoryFactory|null
    {
        return $this->repositoryFactory;
    }

    /**
     * Set the metadata factory class name to use
     *
     * @see \Doctrine\ORM\Configuration::setClassMetadataFactoryName()
     */
    public function setClassMetadataFactoryName(string $factoryName): self
    {
        $this->classMetadataFactoryName = $factoryName;

        return $this;
    }

    public function getClassMetadataFactoryName(): ?string
    {
        return $this->classMetadataFactoryName;
    }

    public function setEntityListenerResolver(string|EntityListenerResolver|null $entityListenerResolver): self
    {
        $this->entityListenerResolver = $entityListenerResolver;

        return $this;
    }

    public function getEntityListenerResolver(): string|EntityListenerResolver|null
    {
        return $this->entityListenerResolver;
    }

    /**
     * @param  mixed[] $secondLevelCache
     */
    public function setSecondLevelCache(array $secondLevelCache): self
    {
        $this->secondLevelCache = new SecondLevelCacheConfiguration($secondLevelCache);

        return $this;
    }

    public function getSecondLevelCache(): SecondLevelCacheConfiguration
    {
        return $this->secondLevelCache ?: new SecondLevelCacheConfiguration();
    }

    public function setFilterSchemaAssetsExpression(string $filterSchemaAssetsExpression): self
    {
        $this->filterSchemaAssetsExpression = $filterSchemaAssetsExpression;

        return $this;
    }

    public function getFilterSchemaAssetsExpression(): ?string
    {
        return $this->filterSchemaAssetsExpression;
    }

    public function setSchemaAssetsFilter(callable $schemaAssetsFilter): self
    {
        $this->schemaAssetsFilter = $schemaAssetsFilter;

        return $this;
    }

    public function getSchemaAssetsFilter(): ?callable
    {
        return $this->schemaAssetsFilter;
    }

    /**
     * Sets default repository class.
     */
    public function setDefaultRepositoryClassName(string $className): self
    {
        $this->defaultRepositoryClassName = $className;

        return $this;
    }

    /**
     * Get default repository class name.
     */
    public function getDefaultRepositoryClassName(): ?string
    {
        return $this->defaultRepositoryClassName;
    }

    /**
     * @param array<class-string<Middleware>> $middlewares
     */
    public function setMiddlewares(array $middlewares): void
    {
        $this->middlewares = $middlewares;
    }

    /**
     * @return array<class-string<Middleware>>
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }
}
