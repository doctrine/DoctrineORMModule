<?php

namespace DoctrineORMModule;

/**
 * Config provider for DoctrineORMModule config
 *
 * @license MIT
 * @link    www.doctrine-project.org
 * @author  James Titcumb <james@asgrim.com>
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
