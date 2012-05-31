<?php

namespace DoctrineORMModule\Service;

use DoctrineModule\Service\DBAL\AbstractConfigurationFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

class ConfigurationFactory extends AbstractConfigurationFactory
{
    /**
     * @var name
     */
    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $name       = $this->name;
        $cfg        = $serviceLocator->get('Configuration');
        $userConfig = isset($cfg['doctrine_orm_config'][$name]) ? $cfg['doctrine_orm_config'][$name] : null;

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

    protected function getIdentifier()
    {
        return 'DoctrineORMModule';
    }
}