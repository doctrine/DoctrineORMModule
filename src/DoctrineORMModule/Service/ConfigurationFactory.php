<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace DoctrineORMModule\Service;

use Doctrine\ORM\Cache\CacheConfiguration;
use Doctrine\ORM\Cache\DefaultCacheFactory;
use Doctrine\ORM\Cache\RegionsConfiguration;
use Doctrine\ORM\Mapping\EntityListenerResolver;
use DoctrineORMModule\Options\Configuration as DoctrineORMModuleConfiguration;
use DoctrineORMModule\Service\DBALConfigurationFactory as DoctrineConfigurationFactory;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\Exception\InvalidArgumentException;
use Doctrine\ORM\Configuration;

class ConfigurationFactory extends DoctrineConfigurationFactory
{
    /**
     * {@inheritDoc}
     *
     * @return Configuration
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var $options DoctrineORMModuleConfiguration */
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
            $config->addNamedNativeQuery($name, $query['sql'], new $query['rsm']);
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

        if ($namingStrategy = $options->getNamingStrategy()) {
            if (is_string($namingStrategy)) {
                if (! $container->has($namingStrategy)) {
                    throw new InvalidArgumentException(sprintf('Naming strategy "%s" not found', $namingStrategy));
                }

                $config->setNamingStrategy($container->get($namingStrategy));
            } else {
                $config->setNamingStrategy($namingStrategy);
            }
        }

        if ($quoteStrategy = $options->getQuoteStrategy()) {
            if (is_string($quoteStrategy)) {
                if (! $container->has($quoteStrategy)) {
                    throw new InvalidArgumentException(sprintf('Quote strategy "%s" not found', $quoteStrategy));
                }

                $config->setQuoteStrategy($container->get($quoteStrategy));
            } else {
                $config->setQuoteStrategy($quoteStrategy);
            }
        }

        if ($repositoryFactory = $options->getRepositoryFactory()) {
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

        if ($entityListenerResolver = $options->getEntityListenerResolver()) {
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

                if (isset($regionConfig['lock_lifetime'])) {
                    $regionsConfig->setLockLifetime($regionName, $regionConfig['lock_lifetime']);
                }
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
        
        if($filterSchemaAssetsExpression = $options->getFilterSchemaAssetsExpression()) {
            $config->setFilterSchemaAssetsExpression($filterSchemaAssetsExpression);
        }

        if ($className = $options->getDefaultRepositoryClassName()) {
            $config->setDefaultRepositoryClassName($className);
        }

        $this->setupDBALConfiguration($container, $config);

        return $config;
    }

    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, Configuration::class);
    }

    /**
     * @return string
     */
    protected function getOptionsClass()
    {
        return DoctrineORMModuleConfiguration::class;
    }
}
