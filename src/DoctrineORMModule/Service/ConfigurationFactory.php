<?php

namespace DoctrineORMModule\Service;

use DoctrineModule\Service\ConfigurationFactory as DoctrineConfigurationFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

class ConfigurationFactory extends DoctrineConfigurationFactory
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $options = $this->getOptions($serviceLocator);
        $config  = new \Doctrine\ORM\Configuration;

        $config->setAutoGenerateProxyClasses($options->generateProxies);
        $config->setProxyDir($options->proxyDir);
        $config->setProxyNamespace($options->proxyNamespace);

        $config->setEntityNamespaces($options->entityNamespaces);

        $config->setCustomDatetimeFunctions($options->datetimeFunctions);
        $config->setCustomStringFunctions($options->stringFunctions);
        $config->setCustomNumericFunctions($options->numericFunctions);

        foreach($options->namedQueries as $query) {
            $config->addNamedQuery($query['name'], $query['dql']);
        }

        foreach($options->namedNativeQueries as $query) {
            $config->addNamedNativeQuery($query['name'], $query['sql'], new $query['rsm']);
        }

        $config->setMetadataCacheImpl($serviceLocator->get($options->metadataCache));
        $config->setQueryCacheImpl($serviceLocator->get($options->queryCache));

        $config->setMetadataDriverImpl($serviceLocator->get($options->driver));

        $this->setupDBALConfiguration($serviceLocator, $config);

        return $config;
    }

    protected function getOptionsClass()
    {
        return 'DoctrineORMModule\Options\Configuration';
    }
}