<?php

declare(strict_types=1);

namespace DoctrineORMModule\Service;

use Doctrine\ORM\Tools\ResolveTargetEntityListener;
use DoctrineModule\Service\AbstractFactory;
use DoctrineORMModule\Options\EntityResolver;
use Psr\Container\ContainerInterface;

use function assert;

final class EntityResolverFactory extends AbstractFactory
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
     * Get the class name of the options associated with this factory.
     */
    public function getOptionsClass(): string
    {
        return EntityResolver::class;
    }
}
