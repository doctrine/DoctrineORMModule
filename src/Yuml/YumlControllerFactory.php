<?php

declare(strict_types=1);

namespace DoctrineORMModule\Yuml;

use Laminas\Http\Client;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

use function sprintf;

class YumlControllerFactory implements FactoryInterface
{
    /**
     * Create an object
     *
     * {@inheritDoc}
     */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null
    ): YumlController {
        $config = $container->get('config');

        if (
            ! isset($config['laminas-developer-tools']['toolbar']['enabled'])
            || ! $config['laminas-developer-tools']['toolbar']['enabled']
        ) {
            throw new ServiceNotFoundException(
                sprintf('Service %s could not be found', YumlController::class)
            );
        }

        return new YumlController(
            new Client('https://yuml.me/diagram/plain/class/', ['timeout' => 30])
        );
    }
}
