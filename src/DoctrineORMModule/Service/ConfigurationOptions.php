<?php

namespace DoctrineORMModule\Service;

use DoctrineModule\Service\AbstractConfigurationOptions;

class ConfigurationOptions extends AbstractConfigurationOptions
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
     * Set the driver key for the metadata driver. Driver key
     * is assembeled as "doctrine.driver.{key}" and pulled from
     * service locator.
     *
     * @var string
     */
    protected $driver = 'array';

    /**
     * @var bool
     */
    protected $generateProxies = true;

    /**
     * @var string
     */
    protected $proxyDir = 'data';

    /**
     * @var string
     */
    protected $proxyNamespace = 'DoctrineORMModule\Proxy';

    /**
     * @var array
     */
    protected $entityNamespaces = array();

    /**
     * @var array
     */
    protected $datetimeFunctions = array();

    /**
     * @var array
     */
    protected $stringFunctions = array();

    /**
     * @var array
     */
    protected $numericFunctions = array();

    /**
     * @var array
     */
    protected $namedQueries = array();

    /**
     * @var array
     */
    protected $namedNativeQueries = array();

    /**
     * @param array $datetimeFunctions
     */
    public function setDatetimeFunctions($datetimeFunctions)
    {
        $this->datetimeFunctions = $datetimeFunctions;
    }

    /**
     * @return array
     */
    public function getDatetimeFunctions()
    {
        return $this->datetimeFunctions;
    }

    /**
     * @param string $driver
     */
    public function setDriver($driver)
    {
        $this->driver = $driver;
    }

    /**
     * @return string
     */
    public function getDriver()
    {
        return "doctrine.driver.{$this->driver}";
    }

    /**
     * @param array $entityNamespaces
     */
    public function setEntityNamespaces($entityNamespaces)
    {
        $this->entityNamespaces = $entityNamespaces;
    }

    /**
     * @return array
     */
    public function getEntityNamespaces()
    {
        return $this->entityNamespaces;
    }

    /**
     * @param boolean $generateProxies
     */
    public function setGenerateProxies($generateProxies)
    {
        $this->generateProxies = $generateProxies;
    }

    /**
     * @return boolean
     */
    public function getGenerateProxies()
    {
        return $this->generateProxies;
    }

    /**
     * @param string $metadataCache
     */
    public function setMetadataCache($metadataCache)
    {
        $this->metadataCache = $metadataCache;
    }

    /**
     * @return string
     */
    public function getMetadataCache()
    {
        return "doctrine.cache.{$this->metadataCache}";
    }

    /**
     * @param array $namedNativeQueries
     */
    public function setNamedNativeQueries($namedNativeQueries)
    {
        $this->namedNativeQueries = $namedNativeQueries;
    }

    /**
     * @return array
     */
    public function getNamedNativeQueries()
    {
        return $this->namedNativeQueries;
    }

    /**
     * @param array $namedQueries
     */
    public function setNamedQueries($namedQueries)
    {
        $this->namedQueries = $namedQueries;
    }

    /**
     * @return array
     */
    public function getNamedQueries()
    {
        return $this->namedQueries;
    }

    /**
     * @param array $numericFunctions
     */
    public function setNumericFunctions($numericFunctions)
    {
        $this->numericFunctions = $numericFunctions;
    }

    /**
     * @return array
     */
    public function getNumericFunctions()
    {
        return $this->numericFunctions;
    }

    /**
     * @param string $proxyDir
     */
    public function setProxyDir($proxyDir)
    {
        $this->proxyDir = $proxyDir;
    }

    /**
     * @return string
     */
    public function getProxyDir()
    {
        return $this->proxyDir;
    }

    /**
     * @param string $proxyNamespace
     */
    public function setProxyNamespace($proxyNamespace)
    {
        $this->proxyNamespace = $proxyNamespace;
    }

    /**
     * @return string
     */
    public function getProxyNamespace()
    {
        return $this->proxyNamespace;
    }

    /**
     * @param string $queryCache
     */
    public function setQueryCache($queryCache)
    {
        $this->queryCache = $queryCache;
    }

    /**
     * @return string
     */
    public function getQueryCache()
    {
        return "doctrine.cache.{$this->queryCache}";
    }

    /**
     * @param array $stringFunctions
     */
    public function setStringFunctions($stringFunctions)
    {
        $this->stringFunctions = $stringFunctions;
    }

    /**
     * @return array
     */
    public function getStringFunctions()
    {
        return $this->stringFunctions;
    }
}