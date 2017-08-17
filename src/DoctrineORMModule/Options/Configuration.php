<?php

namespace DoctrineORMModule\Options;

use Doctrine\ORM\Mapping\EntityListenerResolver;
use Doctrine\ORM\Mapping\NamingStrategy;
use Doctrine\ORM\Mapping\QuoteStrategy;
use Doctrine\ORM\Repository\RepositoryFactory;
use Zend\Stdlib\Exception\InvalidArgumentException;

/**
 * Configuration options for an ORM Configuration
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Kyle Spraggs <theman@spiffyjr.me>
 * @author  Marco Pivetta <ocramius@gmail.com>
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
     * @var array
     */
    protected $entityNamespaces = [];

    /**
     * Keys must be function names and values the FQCN of the implementing class.
     * The function names will be case-insensitive in DQL.
     *
     * @var array
     */
    protected $datetimeFunctions = [];

    /**
     * Keys must be function names and values the FQCN of the implementing class.
     * The function names will be case-insensitive in DQL.
     *
     * @var array
     */
    protected $stringFunctions = [];

    /**
     * Keys must be function names and values the FQCN of the implementing class.
     * The function names will be case-insensitive in DQL.
     *
     * @var array
     */
    protected $numericFunctions = [];

    /**
     * Keys must be the name of the custom filter and the value must be
     * the class name for the custom filter.
     *
     * @var array
     */
    protected $filters = [];

    /**
     * Keys must be the name of the query and values the DQL query string.
     *
     * @var array
     */
    protected $namedQueries = [];

    /**
     * Keys must be the name of the query and the value is an array containing
     * the keys 'sql' for native SQL query string and 'rsm' for the Query\ResultSetMapping.
     *
     * @var array
     */
    protected $namedNativeQueries = [];

    /**
     * Keys must be the name of the custom hydration method and the value must be
     * the class name for the custom hydrator
     *
     * @var array
     */
    protected $customHydrationModes = [];

    /**
     * Naming strategy or name of the naming strategy service to be set in ORM
     * configuration (if any)
     *
     * @var string|null|NamingStrategy
     */
    protected $namingStrategy;

    /**
     * Quote strategy or name of the quote strategy service to be set in ORM
     * configuration (if any)
     *
     * @var string|null|QuoteStrategy
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
     * @var string|null|RepositoryFactory
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
     * @var  string|null|EntityListenerResolver
     */
    protected $entityListenerResolver;

    /**
     * Configuration for second level cache
     *
     * @link http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/second-level-cache.html
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
     * @param  array $datetimeFunctions
     * @return self
     */
    public function setDatetimeFunctions($datetimeFunctions)
    {
        $this->datetimeFunctions = $datetimeFunctions;

        return $this;
    }

    /**
     * @return array
     */
    public function getDatetimeFunctions()
    {
        return $this->datetimeFunctions;
    }

    /**
     * @param  string $driver
     * @return self
     */
    public function setDriver($driver)
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * @return string
     */
    public function getDriver()
    {
        return "doctrine.driver.{$this->driver}";
    }

    /**
     * @param  array $entityNamespaces
     * @return self
     */
    public function setEntityNamespaces($entityNamespaces)
    {
        $this->entityNamespaces = $entityNamespaces;

        return $this;
    }

    /**
     * @return array
     */
    public function getEntityNamespaces()
    {
        return $this->entityNamespaces;
    }

    /**
     * @param  boolean $generateProxies
     * @return self
     */
    public function setGenerateProxies($generateProxies)
    {
        $this->generateProxies = $generateProxies;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getGenerateProxies()
    {
        return $this->generateProxies;
    }

    /**
     * @param  string $metadataCache
     * @return self
     */
    public function setMetadataCache($metadataCache)
    {
        $this->metadataCache = $metadataCache;

        return $this;
    }

    /**
     * @return string
     */
    public function getMetadataCache()
    {
        return "doctrine.cache.{$this->metadataCache}";
    }

    /**
     * @param  string $resultCache
     * @return self
     */
    public function setResultCache($resultCache)
    {
        $this->resultCache = $resultCache;

        return $this;
    }

    /**
     * @return string
     */
    public function getResultCache()
    {
        return "doctrine.cache.{$this->resultCache}";
    }

    /**
     * @param  string $hydrationCache
     * @return self
     */
    public function setHydrationCache($hydrationCache)
    {
        $this->hydrationCache = $hydrationCache;

        return $this;
    }

    /**
     * @return string
     */
    public function getHydrationCache()
    {
        return "doctrine.cache.{$this->hydrationCache}";
    }

    /**
     * @param  array $namedNativeQueries
     * @return self
     */
    public function setNamedNativeQueries($namedNativeQueries)
    {
        $this->namedNativeQueries = $namedNativeQueries;

        return $this;
    }

    /**
     * @return array
     */
    public function getNamedNativeQueries()
    {
        return $this->namedNativeQueries;
    }

    /**
     * @param  array $namedQueries
     * @return self
     */
    public function setNamedQueries($namedQueries)
    {
        $this->namedQueries = $namedQueries;

        return $this;
    }

    /**
     * @return array
     */
    public function getNamedQueries()
    {
        return $this->namedQueries;
    }

    /**
     * @param  array $numericFunctions
     * @return self
     */
    public function setNumericFunctions($numericFunctions)
    {
        $this->numericFunctions = $numericFunctions;

        return $this;
    }

    /**
     * @return array
     */
    public function getNumericFunctions()
    {
        return $this->numericFunctions;
    }

    /**
     *
     * @param  array $filters
     * @return self
     */
    public function setFilters($filters)
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     *
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param  string $proxyDir
     * @return self
     */
    public function setProxyDir($proxyDir)
    {
        $this->proxyDir = $proxyDir;

        return $this;
    }

    /**
     * @return string
     */
    public function getProxyDir()
    {
        return $this->proxyDir;
    }

    /**
     * @param  string $proxyNamespace
     * @return self
     */
    public function setProxyNamespace($proxyNamespace)
    {
        $this->proxyNamespace = $proxyNamespace;

        return $this;
    }

    /**
     * @return string
     */
    public function getProxyNamespace()
    {
        return $this->proxyNamespace;
    }

    /**
     * @param  string $queryCache
     * @return self
     */
    public function setQueryCache($queryCache)
    {
        $this->queryCache = $queryCache;

        return $this;
    }

    /**
     * @return string
     */
    public function getQueryCache()
    {
        return "doctrine.cache.{$this->queryCache}";
    }

    /**
     * @param  array $stringFunctions
     * @return self
     */
    public function setStringFunctions($stringFunctions)
    {
        $this->stringFunctions = $stringFunctions;

        return $this;
    }

    /**
     * @return array
     */
    public function getStringFunctions()
    {
        return $this->stringFunctions;
    }

    /**
     * @param  array $modes
     * @return self
     */
    public function setCustomHydrationModes($modes)
    {
        $this->customHydrationModes = $modes;

        return $this;
    }

    /**
     * @return array
     */
    public function getCustomHydrationModes()
    {
        return $this->customHydrationModes;
    }

    /**
     * @param  string|null|NamingStrategy $namingStrategy
     * @return self
     * @throws InvalidArgumentException   when the provided naming strategy does not fit the expected type
     */
    public function setNamingStrategy($namingStrategy)
    {
        if (null === $namingStrategy
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
     * @return string|null|NamingStrategy
     */
    public function getNamingStrategy()
    {
        return $this->namingStrategy;
    }

    /**
     * @param  string|null|QuoteStrategy $quoteStrategy
     * @return self
     * @throws InvalidArgumentException   when the provided quote strategy does not fit the expected type
     */
    public function setQuoteStrategy($quoteStrategy)
    {
        if (null === $quoteStrategy
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
     * @return string|null|QuoteStrategy
     */
    public function getQuoteStrategy()
    {
        return $this->quoteStrategy;
    }

    /**
     * @param  string|null|RepositoryFactory $repositoryFactory
     * @return self
     * @throws InvalidArgumentException   when the provided repository factory does not fit the expected type
     */
    public function setRepositoryFactory($repositoryFactory)
    {
        if (null === $repositoryFactory
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
     * @return string|null|RepositoryFactory
     */
    public function getRepositoryFactory()
    {
        return $this->repositoryFactory;
    }

    /**
     * Set the metadata factory class name to use
     *
     * @see \Doctrine\ORM\Configuration::setClassMetadataFactoryName()
     *
     * @param string $factoryName
     * @return self
     */
    public function setClassMetadataFactoryName($factoryName)
    {
        $this->classMetadataFactoryName = (string) $factoryName;

        return $this;
    }

    /**
     * @return string
     */
    public function getClassMetadataFactoryName()
    {
        return $this->classMetadataFactoryName;
    }

    /**
     * @param  string|null|EntityListenerResolver $entityListenerResolver
     * @return self
     * @throws InvalidArgumentException           When the provided entity listener resolver
     *                                            does not fit the expected type
     */
    public function setEntityListenerResolver($entityListenerResolver)
    {
        if (null === $entityListenerResolver
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
     * @return string|null|EntityListenerResolver
     */
    public function getEntityListenerResolver()
    {
        return $this->entityListenerResolver;
    }

    /**
     * @param  array $secondLevelCache
     * @return self
     */
    public function setSecondLevelCache(array $secondLevelCache)
    {
        $this->secondLevelCache = new SecondLevelCacheConfiguration($secondLevelCache);

        return $this;
    }

    /**
     * @return SecondLevelCacheConfiguration
     */
    public function getSecondLevelCache()
    {
        return $this->secondLevelCache ?: new SecondLevelCacheConfiguration();
    }

    /**
     * @param  string $filterSchemaAssetsExpression
     * @return self
     */
    public function setFilterSchemaAssetsExpression($filterSchemaAssetsExpression)
    {
        $this->filterSchemaAssetsExpression = $filterSchemaAssetsExpression;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFilterSchemaAssetsExpression()
    {
        return $this->filterSchemaAssetsExpression;
    }

    /**
     * Sets default repository class.
     *
     * @param  string $className
     * @return self
     */
    public function setDefaultRepositoryClassName($className)
    {
        $this->defaultRepositoryClassName = (string) $className;

        return $this;
    }

    /**
     * Get default repository class name.
     *
     * @return string|null
     */
    public function getDefaultRepositoryClassName()
    {
        return $this->defaultRepositoryClassName;
    }
}
