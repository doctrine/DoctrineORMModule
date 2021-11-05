<?php

declare(strict_types=1);

namespace DoctrineORMModule\Options;

use Doctrine\ORM\Mapping\EntityListenerResolver;
use Doctrine\ORM\Mapping\NamingStrategy;
use Doctrine\ORM\Mapping\QuoteStrategy;
use Doctrine\ORM\Repository\RepositoryFactory;
use Laminas\Stdlib\Exception\InvalidArgumentException;

use function get_class;
use function gettype;
use function is_object;
use function is_string;
use function sprintf;

/**
 * Configuration options for an ORM Configuration
 */
class Configuration extends DBALConfiguration
{
    /**
     * Set the cache key for the metadata cache. Cache key
     * is assembled as "doctrine.cache.{key}" and pulled from
     * service locator.
     *
     * @var string
     */
    protected $metadataCache = 'array';

    /**
     * Set the cache key for the query cache. Cache key
     * is assembled as "doctrine.cache.{key}" and pulled from
     * service locator.
     *
     * @var string
     */
    protected $queryCache = 'array';

    /**
     * Set the cache key for the result cache. Cache key
     * is assembled as "doctrine.cache.{key}" and pulled from
     * service locator.
     *
     * @var string
     */
    protected $resultCache = 'array';

    /**
     * Set the cache key for the hydration cache. Cache key
     * is assembled as "doctrine.cache.{key}" and pulled from
     * service locator.
     *
     * @var string
     */
    protected $hydrationCache = 'array';

    /**
     * Set the driver key for the metadata driver. Driver key
     * is assembled as "doctrine.driver.{key}" and pulled from
     * service locator.
     *
     * @var string
     */
    protected $driver = 'orm_default';

    /**
     * Automatic generation of proxies (disable for production!)
     *
     * @var bool
     */
    protected $generateProxies = true;

    /**
     * Proxy directory.
     *
     * @var string
     */
    protected $proxyDir = 'data';

    /**
     * Proxy namespace.
     *
     * @var string
     */
    protected $proxyNamespace = 'DoctrineORMModule\Proxy';

    /**
     * Entity alias map.
     *
     * @var mixed[]
     */
    protected $entityNamespaces = [];

    /**
     * Keys must be function names and values the FQCN of the implementing class.
     * The function names will be case-insensitive in DQL.
     *
     * @var mixed[]
     */
    protected $datetimeFunctions = [];

    /**
     * Keys must be function names and values the FQCN of the implementing class.
     * The function names will be case-insensitive in DQL.
     *
     * @var mixed[]
     */
    protected $stringFunctions = [];

    /**
     * Keys must be function names and values the FQCN of the implementing class.
     * The function names will be case-insensitive in DQL.
     *
     * @var mixed[]
     */
    protected $numericFunctions = [];

    /**
     * Keys must be the name of the custom filter and the value must be
     * the class name for the custom filter.
     *
     * @var mixed[]
     */
    protected $filters = [];

    /**
     * Keys must be the name of the query and values the DQL query string.
     *
     * @var mixed[]
     */
    protected $namedQueries = [];

    /**
     * Keys must be the name of the query and the value is an array containing
     * the keys 'sql' for native SQL query string and 'rsm' for the Query\ResultSetMapping.
     *
     * @var mixed[]
     */
    protected $namedNativeQueries = [];

    /**
     * Keys must be the name of the custom hydration method and the value must be
     * the class name for the custom hydrator
     *
     * @var mixed[]
     */
    protected $customHydrationModes = [];

    /**
     * Naming strategy or name of the naming strategy service to be set in ORM
     * configuration (if any)
     *
     * @var string|NamingStrategy|null
     */
    protected $namingStrategy;

    /**
     * Quote strategy or name of the quote strategy service to be set in ORM
     * configuration (if any)
     *
     * @var string|QuoteStrategy|null
     */
    protected $quoteStrategy;

    /**
     * Default repository class
     *
     * @var string|null
     */
    protected $defaultRepositoryClassName;

    /**
     * Repository factory or name of the repository factory service to be set in ORM
     * configuration (if any)
     *
     * @var string|RepositoryFactory|null
     */
    protected $repositoryFactory;

    /**
     * Class name of MetaData factory to be set in ORM.
     * The entityManager will create a new instance on construction.
     *
     * @var string
     */
    protected $classMetadataFactoryName;

    /**
     * Entity listener resolver or service name of the entity listener resolver
     * to be set in ORM configuration (if any)
     *
     * @link http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/events.html
     *
     * @var string|EntityListenerResolver|null
     */
    protected $entityListenerResolver;

    /**
     * Configuration for second level cache
     *
     * @link http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/second-level-cache.html
     *
     * @var SecondLevelCacheConfiguration|null
     */
    protected $secondLevelCache;

    /**
     * Configuration option for the filter schema assets expression
     *
     * @var string|null
     */
    protected $filterSchemaAssetsExpression;

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

    /**
     * @param string|NamingStrategy|null $namingStrategy
     *
     * @throws InvalidArgumentException   when the provided naming strategy does not fit the expected type.
     */
    public function setNamingStrategy($namingStrategy): self
    {
        if (
            $namingStrategy === null
            || is_string($namingStrategy)
            || $namingStrategy instanceof NamingStrategy
        ) {
            $this->namingStrategy = $namingStrategy;

            return $this;
        }

        throw new InvalidArgumentException(
            sprintf(
                'namingStrategy must be either a string, a Doctrine\ORM\Mapping\NamingStrategy '
                . 'instance or null, %s given',
                is_object($namingStrategy) ? get_class($namingStrategy) : gettype($namingStrategy)
            )
        );
    }

    /**
     * @return string|NamingStrategy|null
     */
    public function getNamingStrategy()
    {
        return $this->namingStrategy;
    }

    /**
     * @param string|QuoteStrategy|null $quoteStrategy
     *
     * @throws InvalidArgumentException   when the provided quote strategy does not fit the expected type.
     */
    public function setQuoteStrategy($quoteStrategy): self
    {
        if (
            $quoteStrategy === null
            || is_string($quoteStrategy)
            || $quoteStrategy instanceof QuoteStrategy
        ) {
            $this->quoteStrategy = $quoteStrategy;

            return $this;
        }

        throw new InvalidArgumentException(
            sprintf(
                'quoteStrategy must be either a string, a Doctrine\ORM\Mapping\QuoteStrategy '
                . 'instance or null, %s given',
                is_object($quoteStrategy) ? get_class($quoteStrategy) : gettype($quoteStrategy)
            )
        );
    }

    /**
     * @return string|QuoteStrategy|null
     */
    public function getQuoteStrategy()
    {
        return $this->quoteStrategy;
    }

    /**
     * @param string|RepositoryFactory|null $repositoryFactory
     *
     * @throws InvalidArgumentException   when the provided repository factory does not fit the expected type.
     */
    public function setRepositoryFactory($repositoryFactory): self
    {
        if (
            $repositoryFactory === null
            || is_string($repositoryFactory)
            || $repositoryFactory instanceof RepositoryFactory
        ) {
            $this->repositoryFactory = $repositoryFactory;

            return $this;
        }

        throw new InvalidArgumentException(
            sprintf(
                'repositoryFactory must be either a string, a Doctrine\ORM\Repository\RepositoryFactory '
                . 'instance or null, %s given',
                is_object($repositoryFactory) ? get_class($repositoryFactory) : gettype($repositoryFactory)
            )
        );
    }

    /**
     * @return string|RepositoryFactory|null
     */
    public function getRepositoryFactory()
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
        $this->classMetadataFactoryName = (string) $factoryName;

        return $this;
    }

    public function getClassMetadataFactoryName(): ?string
    {
        return $this->classMetadataFactoryName;
    }

    /**
     * @param string|EntityListenerResolver|null $entityListenerResolver
     *
     * @throws InvalidArgumentException           When the provided entity listener resolver
     *                                            does not fit the expected type.
     */
    public function setEntityListenerResolver($entityListenerResolver): self
    {
        if (
            $entityListenerResolver === null
            || $entityListenerResolver instanceof EntityListenerResolver
            || is_string($entityListenerResolver)
        ) {
            $this->entityListenerResolver = $entityListenerResolver;

            return $this;
        }

        throw new InvalidArgumentException(sprintf(
            'entityListenerResolver must be either a string, a Doctrine\ORM\Mapping\EntityListenerResolver '
            . 'instance or null, %s given',
            is_object($entityListenerResolver) ? get_class($entityListenerResolver) : gettype($entityListenerResolver)
        ));
    }

    /**
     * @return string|EntityListenerResolver|null
     */
    public function getEntityListenerResolver()
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

    /**
     * Sets default repository class.
     */
    public function setDefaultRepositoryClassName(string $className): self
    {
        $this->defaultRepositoryClassName = (string) $className;

        return $this;
    }

    /**
     * Get default repository class name.
     */
    public function getDefaultRepositoryClassName(): ?string
    {
        return $this->defaultRepositoryClassName;
    }
}
