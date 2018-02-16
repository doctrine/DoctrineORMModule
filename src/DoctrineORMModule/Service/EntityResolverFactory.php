<?php

namespace DoctrineORMModule\Service;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Tools\ResolveTargetEntityListener;
use DoctrineModule\Service\AbstractFactory;
use DoctrineORMModule\Options\EntityResolver;
use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManager;
use Zend\ServiceManager\ServiceLocatorInterface;

class EntityResolverFactory extends AbstractFactory
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var $options EntityResolver */
        $options      = $this->getOptions($container, 'entity_resolver');
        $eventManager = $container->get($options->getEventManager());
        $resolvers    = $options->getResolvers();

        $targetEntityListener = new ResolveTargetEntityListener();

        foreach ($resolvers as $oldEntity => $newEntity) {
            $targetEntityListener->addResolveTargetEntity($oldEntity, $newEntity, []);
        }

        // Starting from Doctrine ORM 2.5, the listener implements EventSubscriber
        if ($targetEntityListener instanceof EventSubscriber) {
            $eventManager->addEventSubscriber($targetEntityListener);
        } else {
            $eventManager->addEventListener(Events::loadClassMetadata, $targetEntityListener);
        }

        return $eventManager;
    }

    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, EventManager::class);
    }

    /**
     * Get the class name of the options associated with this factory.
     *
     * @return string
     */
    public function getOptionsClass()
    {
        return EntityResolver::class;
    }
}
