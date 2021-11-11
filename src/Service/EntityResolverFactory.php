<?php

declare(strict_types=1);

namespace DoctrineORMModule\Service;

use Doctrine\ORM\Tools\ResolveTargetEntityListener;
use DoctrineModule\Service\AbstractFactory;
use DoctrineORMModule\Options\EntityResolver;
use Interop\Container\ContainerInterface;
use Laminas\EventManager\EventManager;
use Laminas\ServiceManager\ServiceLocatorInterface;

use function assert;

class EntityResolverFactory extends AbstractFactory
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $options = $this->getOptions($container, 'entity_resolver');
        assert($options instanceof EntityResolver);
        $eventManager = $container->get($options->getEventManager());
        $resolvers    = $options->getResolvers();

        $targetEntityListener = new ResolveTargetEntityListener();

        foreach ($resolvers as $oldEntity => $newEntity) {
            $targetEntityListener->addResolveTargetEntity($oldEntity, $newEntity, []);
        }

        $eventManager->addEventSubscriber($targetEntityListener);

        return $eventManager;
    }

    /**
     * {@inheritDoc}
     *
     * @deprecated 4.1.0 With laminas-servicemanager v3 this method is obsolete and will be removed in 5.0.0.
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, EventManager::class);
    }

    /**
     * Get the class name of the options associated with this factory.
     */
    public function getOptionsClass(): string
    {
        return EntityResolver::class;
    }
}
