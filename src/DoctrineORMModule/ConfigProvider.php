<?php

namespace DoctrineORMModule;

/**
 * Config provider for DoctrineORMModule config
 */
class ConfigProvider
{
    /**
     * @return array
     */
    public function __invoke()
    {
        $config = include __DIR__ . '/../../config/module.config.php';
        $config['dependencies'] = $config['service_manager'];
        unset($config['service_manager']);
        return $config;
    }
}
