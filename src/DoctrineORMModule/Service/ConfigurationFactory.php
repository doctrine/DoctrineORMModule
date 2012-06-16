<?php

namespace DoctrineORMModule\Service;

use DoctrineModule\Service\ConfigurationFactory as DoctrineConfigurationFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

class ConfigurationFactory extends DoctrineConfigurationFactory
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var $options \DoctrineORMModule\Options\Configuration */
        $options = $this->getOptions($serviceLocator);
        $config  = new \Doctrine\ORM\Configuration;

        $config->setAutoGenerateProxyClasses($options->getGenerateProxies());
        $config->setProxyDir($options->getProxyDir());
        $config->setProxyNamespace($options->getProxyNamespace());

        $config->setEntityNamespaces($options->getEntityNamespaces());

        $config->setCustomDatetimeFunctions($options->getDatetimeFunctions());
        $config->setCustomStringFunctions($options->getStringFunctions());
        $config->setCustomNumericFunctions($options->getNumericFunctions());

        foreach($options->getNamedQueries() as $name => $query) {
            $config->addNamedQuery($name, $query);
        }

        foreach($options->getNamedNativeQueries() as $name => $query) {
            $config->addNamedNativeQuery($name, $query['sql'], new $query['rsm']);
        }

        $config->setMetadataCacheImpl($serviceLocator->get($options->getMetadataCache()));
        $config->setQueryCacheImpl($serviceLocator->get($options->getQueryCache()));

        $config->setMetadataDriverImpl($serviceLocator->get($options->getDriver()));

        $this->setupDBALConfiguration($serviceLocator, $config);

        return $config;
    }

    protected function getOptionsClass()
    {
        return 'DoctrineORMModule\Options\Configuration';
    }
}