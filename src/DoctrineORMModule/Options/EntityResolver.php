<?php

namespace DoctrineORMModule\Options;

use InvalidArgumentException;
use Zend\Stdlib\AbstractOptions;

class EntityResolver
{
    /**
     * Set the configuration key for the EventManager. Event manager key
     * is assembled as "doctrine.eventmanager.{key}" and pulled from
     * service locator.
     *
     * @var string
     */
    protected $eventManager = 'orm_default';

    /**
     * An array that maps a class name (or interface name) to another class
     * name (and with an optional mapping)
     *
     * @var array
     */
    protected $resolvers = array();


    /**
     * @param  string $eventManager
     * @return self
     */
    public function setEventManager($eventManager)
    {
        $this->eventManager = $eventManager;

        return $this;
    }

    /**
     * @return string
     */
    public function getEventManager()
    {
        return "doctrine.eventmanager.{$this->eventManager}";
    }

    /**
     * @param array $resolvers
     * @throws \InvalidArgumentException
     */
    public function setResolvers(array $resolvers)
    {
        foreach ($resolvers as $entity => $resolveParameters) {
            if (!isset($resolveParameters['resolved_entity_name'])) {
                throw new InvalidArgumentException(
                    'The resolved entity name for %s has not been set',
                    $entity
                );
            }

            if (!isset($resolveParameters['mapping'])) {
                $resolveParameters['mapping'] = array();
            }

            $this->resolvers[$entity] = $resolveParameters;
        }
    }

    /**
     * @return array
     */
    public function getResolvers()
    {
        return $this->resolvers;
    }
}
