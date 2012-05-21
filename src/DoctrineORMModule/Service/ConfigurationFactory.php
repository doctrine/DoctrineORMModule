<?php

namespace DoctrineORMModule\Service;

use DoctrineModule\Service\AbstractConfigurationFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

class ConfigurationFactory extends AbstractConfigurationFactory
{
    public function createService(ServiceLocatorInterface $sl)
    {
        $userConfig = $sl->get('Configuration')->doctrine_orm_config;
        $config     = new \Doctrine\ORM\Configuration;

        $config->setAutoGenerateProxyClasses($userConfig->proxy_auto_generate);
        $config->setProxyDir($userConfig->proxy_dir);
        $config->setProxyNamespace($userConfig->proxy_namespace);

        $config->setEntityNamespaces($userConfig->entity_namespaces->toArray());

        $config->setCustomDatetimeFunctions($userConfig->custom_datetime_functions->toArray());
        $config->setCustomStringFunctions($userConfig->custom_string_functions->toArray());
        $config->setCustomNumericFunctions($userConfig->custom_numeric_functions->toArray());

        foreach($userConfig->named_queries as $query) {
            $config->addNamedQuery($query->name, $query->dql);
        }

        foreach($userConfig->named_native_queries as $query) {
            $config->addNamedNativeQuery($query->name, $query->sql, new $query->rsm);
        }

        $config->setMetadataCacheImpl($sl->get('doctrine_orm_metadata_cache'));
        $config->setQueryCacheImpl($sl->get('doctrine_orm_query_cache'));
        $config->setResultCacheImpl($sl->get('doctrine_orm_result_cache'));

        $config->setSQLLogger($userConfig->sql_logger);

        $config->setMetadataDriverImpl($this->getDriverChain($sl, $config));

        return $config;
    }
}