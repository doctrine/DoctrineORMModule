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
use Laminas\ServiceManager\Exception\InvalidArgumentException;
use Psr\Container\ContainerInterface;

use function is_string;
use function method_exists;
use function sprintf;

final class ConfigurationFactory extends DoctrineConfigurationFactory
{
    /**
     * {@inheritDoc}
     *
     * @param string $requestedName
     *
     * @return Configuration
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, ?array $options = null)
    {
        $options = $this->getOptions($serviceLocator);
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

        $config->setMetadataCacheImpl($serviceLocator->get($options->getMetadataCache()));
        $config->setQueryCacheImpl($serviceLocator->get($options->getQueryCache()));
        $config->setResultCacheImpl($serviceLocator->get($options->getResultCache()));
        $config->setHydrationCacheImpl($serviceLocator->get($options->getHydrationCache()));
        $config->setMetadataDriverImpl($serviceLocator->get($options->getDriver()));

        $namingStrategy = $options->getNamingStrategy();
        if ($namingStrategy) {
            if (is_string($namingStrategy)) {
                if (! $serviceLocator->has($namingStrategy)) {
                    throw new InvalidArgumentException(sprintf('Naming strategy "%s" not found', $namingStrategy));
                }

                $config->setNamingStrategy($serviceLocator->get($namingStrategy));
            } else {
                $config->setNamingStrategy($namingStrategy);
            }
        }

        $quoteStrategy = $options->getQuoteStrategy();
        if ($quoteStrategy) {
            if (is_string($quoteStrategy)) {
                if (! $serviceLocator->has($quoteStrategy)) {
                    throw new InvalidArgumentException(sprintf('Quote strategy "%s" not found', $quoteStrategy));
                }

                $config->setQuoteStrategy($serviceLocator->get($quoteStrategy));
            } else {
                $config->setQuoteStrategy($quoteStrategy);
            }
        }

        $repositoryFactory = $options->getRepositoryFactory();
        if ($repositoryFactory) {
            if (is_string($repositoryFactory)) {
                if (! $serviceLocator->has($repositoryFactory)) {
                    throw new InvalidArgumentException(
                        sprintf('Repository factory "%s" not found', $repositoryFactory)
                    );
                }

                $config->setRepositoryFactory($serviceLocator->get($repositoryFactory));
            } else {
                $config->setRepositoryFactory($repositoryFactory);
            }
        }

        $entityListenerResolver = $options->getEntityListenerResolver();
        if ($entityListenerResolver) {
            if ($entityListenerResolver instanceof EntityListenerResolver) {
                $config->setEntityListenerResolver($entityListenerResolver);
            } else {
                $config->setEntityListenerResolver($serviceLocator->get($entityListenerResolver));
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
            $cacheFactory = new DefaultCacheFactory($regionsConfig, $config->getResultCache());
            $cacheFactory->setFileLockRegionDirectory($secondLevelCache->getFileLockRegionDirectory());

            $cacheConfiguration = new CacheConfiguration();
            $cacheConfiguration->setCacheFactory($cacheFactory);
            $cacheConfiguration->setRegionsConfiguration($regionsConfig);

            $config->setSecondLevelCacheEnabled();
            $config->setSecondLevelCacheConfiguration($cacheConfiguration);
        }

        // only works for DBAL 2.x, not for 3.x
        if (method_exists($config, 'setFilterSchemaAssetsExpression')) {
            $filterSchemaAssetsExpression = $options->getFilterSchemaAssetsExpression();
            if ($filterSchemaAssetsExpression) {
                $config->setFilterSchemaAssetsExpression($filterSchemaAssetsExpression);
            }
        }

        // DBAL 2.x
        if (method_exists($config, 'setSchemaAssetsFilter')) {
            $schemaAssetsFilter = $options->getSchemaAssetsFilter();
            if ($schemaAssetsFilter) {
                $config->setSchemaAssetsFilter($schemaAssetsFilter);
            }
        }

        $className = $options->getDefaultRepositoryClassName();
        if ($className) {
            $config->setDefaultRepositoryClassName($className);
        }

        $this->setupDBALConfiguration($serviceLocator, $config);

        return $config;
    }

    protected function getOptionsClass(): string
    {
        return DoctrineORMModuleConfiguration::class;
    }
}
