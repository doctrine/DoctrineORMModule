<?php

declare(strict_types=1);

namespace DoctrineORMModule\Service;

use Doctrine\ORM\Cache\CacheConfiguration;
use Doctrine\ORM\Cache\DefaultCacheFactory;
use Doctrine\ORM\Cache\RegionsConfiguration;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Mapping\EntityListenerResolver;
use DoctrineORMModule\Options\Configuration as DoctrineORMModuleConfiguration;
use DoctrineORMModule\Service\DBALConfigurationFactory as DoctrineConfigurationFactory;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Exception\InvalidArgumentException;
use Laminas\ServiceManager\ServiceLocatorInterface;
use function is_string;
use function sprintf;

class ConfigurationFactory extends DoctrineConfigurationFactory
{
    /**
     * {@inheritDoc}
     *
     * @return Configuration
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $options = $this->getOptions($container);
        $config  = new Configuration();

        $config->setAutoGenerateProxyClasses($options->getGenerateProxies());
        $config->setProxyDir($options->getProxyDir());
        $config->setProxyNamespace($options->getProxyNamespace());

        $config->setEntityNamespaces($options->getEntityNamespaces());

        $config->setCustomDatetimeFunctions($options->getDatetimeFunctions());
        $config->setCustomStringFunctions($options->getStringFunctions());
        $config->setCustomNumericFunctions($options->getNumericFunctions());

        $config->setClassMetadataFactoryName($options->getClassMetadataFactoryName());

        foreach ($options->getNamedQueries() as $name => $query) {
            $config->addNamedQuery($name, $query);
        }

        foreach ($options->getNamedNativeQueries() as $name => $query) {
            $config->addNamedNativeQuery($name, $query['sql'], new $query['rsm']());
        }

        foreach ($options->getCustomHydrationModes() as $modeName => $hydrator) {
            $config->addCustomHydrationMode($modeName, $hydrator);
        }

        foreach ($options->getFilters() as $name => $class) {
            $config->addFilter($name, $class);
        }

        $config->setMetadataCacheImpl($container->get($options->getMetadataCache()));
        $config->setQueryCacheImpl($container->get($options->getQueryCache()));
        $config->setResultCacheImpl($container->get($options->getResultCache()));
        $config->setHydrationCacheImpl($container->get($options->getHydrationCache()));
        $config->setMetadataDriverImpl($container->get($options->getDriver()));

        $namingStrategy = $options->getNamingStrategy();
        if ($namingStrategy) {
            if (is_string($namingStrategy)) {
                if (! $container->has($namingStrategy)) {
                    throw new InvalidArgumentException(sprintf('Naming strategy "%s" not found', $namingStrategy));
                }

                $config->setNamingStrategy($container->get($namingStrategy));
            } else {
                $config->setNamingStrategy($namingStrategy);
            }
        }

        $quoteStrategy = $options->getQuoteStrategy();
        if ($quoteStrategy) {
            if (is_string($quoteStrategy)) {
                if (! $container->has($quoteStrategy)) {
                    throw new InvalidArgumentException(sprintf('Quote strategy "%s" not found', $quoteStrategy));
                }

                $config->setQuoteStrategy($container->get($quoteStrategy));
            } else {
                $config->setQuoteStrategy($quoteStrategy);
            }
        }

        $repositoryFactory = $options->getRepositoryFactory();
        if ($repositoryFactory) {
            if (is_string($repositoryFactory)) {
                if (! $container->has($repositoryFactory)) {
                    throw new InvalidArgumentException(
                        sprintf('Repository factory "%s" not found', $repositoryFactory)
                    );
                }

                $config->setRepositoryFactory($container->get($repositoryFactory));
            } else {
                $config->setRepositoryFactory($repositoryFactory);
            }
        }

        $entityListenerResolver = $options->getEntityListenerResolver();
        if ($entityListenerResolver) {
            if ($entityListenerResolver instanceof EntityListenerResolver) {
                $config->setEntityListenerResolver($entityListenerResolver);
            } else {
                $config->setEntityListenerResolver($container->get($entityListenerResolver));
            }
        }

        $secondLevelCache = $options->getSecondLevelCache();

        if ($secondLevelCache->isEnabled()) {
            $regionsConfig = new RegionsConfiguration(
                $secondLevelCache->getDefaultLifetime(),
                $secondLevelCache->getDefaultLockLifetime()
            );

            foreach ($secondLevelCache->getRegions() as $regionName => $regionConfig) {
                if (isset($regionConfig['lifetime'])) {
                    $regionsConfig->setLifetime($regionName, $regionConfig['lifetime']);
                }

                if (! isset($regionConfig['lock_lifetime'])) {
                    continue;
                }

                $regionsConfig->setLockLifetime($regionName, $regionConfig['lock_lifetime']);
            }

            // As Second Level Cache caches queries results, we reuse the result cache impl
            $cacheFactory = new DefaultCacheFactory($regionsConfig, $config->getResultCacheImpl());
            $cacheFactory->setFileLockRegionDirectory($secondLevelCache->getFileLockRegionDirectory());

            $cacheConfiguration = new CacheConfiguration();
            $cacheConfiguration->setCacheFactory($cacheFactory);
            $cacheConfiguration->setRegionsConfiguration($regionsConfig);

            $config->setSecondLevelCacheEnabled();
            $config->setSecondLevelCacheConfiguration($cacheConfiguration);
        }

        $filterSchemaAssetsExpression = $options->getFilterSchemaAssetsExpression();
        if ($filterSchemaAssetsExpression) {
            $config->setFilterSchemaAssetsExpression($filterSchemaAssetsExpression);
        }

        $className = $options->getDefaultRepositoryClassName();
        if ($className) {
            $config->setDefaultRepositoryClassName($className);
        }

        $this->setupDBALConfiguration($container, $config);

        return $config;
    }

    /**
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, Configuration::class);
    }

    protected function getOptionsClass() : string
    {
        return DoctrineORMModuleConfiguration::class;
    }
}
