<?php

declare(strict_types=1);

namespace DoctrineORMModule;

/**
 * Config provider for DoctrineORMModule config
 */
final class ConfigProvider
{
    /**
     * @return mixed[]
     */
    public function __invoke(): array
    {
        $config                 = include __DIR__ . '/../config/module.config.php';
        $config['dependencies'] = $config['service_manager'];
        unset($config['service_manager']);

        return $config;
    }
}
