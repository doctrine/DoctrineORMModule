<?php

namespace DoctrineORMModule\Service;

use RuntimeException;
use DoctrineModule\Service\AbstractConfigurationFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

class DefaultConfigurationFactory extends AbstractConfigurationFactory
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $name       = $this->getName();
        $cfg        = $serviceLocator->get('Configuration');
        $userConfig = isset($cfg['doctrine']['orm']['configuration'][$name]) ?
                          $cfg['doctrine']['orm']['configuration'][$name] :
                          null;

        if (null === $userConfig) {
            throw new RuntimeException(sprintf(
                'Configuration with name "%s" could not be found in doctrine_orm_config.',
                $name
            ));
        }

        $config = new \Doctrine\ORM\Configuration;

        $config->setAutoGenerateProxyClasses($userConfig['auto_generate_proxies']);
        $config->setProxyDir($userConfig['proxy_dir']);
        $config->setProxyNamespace($userConfig['proxy_namespace']);

        $config->setEntityNamespaces($userConfig['entity_namespaces']);

        $config->setCustomDatetimeFunctions($userConfig['custom_datetime_functions']);
        $config->setCustomStringFunctions($userConfig['custom_string_functions']);
        $config->setCustomNumericFunctions($userConfig['custom_numeric_functions']);

        foreach($userConfig['named_queries'] as $query) {
            $config->addNamedQuery($query['name'], $query['dql']);
        }

        foreach($userConfig['named_native_queries'] as $query) {
            $config->addNamedNativeQuery($query['name'], $query['sql'], new $query['rsm']);
        }

        $config->setMetadataCacheImpl($serviceLocator->get('doctrine_orm_metadata_cache'));
        $config->setQueryCacheImpl($serviceLocator->get('doctrine_orm_query_cache'));
        $config->setResultCacheImpl($serviceLocator->get('doctrine_orm_result_cache'));

        $config->setSQLLogger($userConfig['sql_logger']);

        $config->setMetadataDriverImpl($this->getDriverChain($serviceLocator, $config));

        return $config;
    }

    public function getName()
    {
        return 'default';
    }

    protected function getIdentifier()
    {
        return 'DoctrineORMModule';
    }
}