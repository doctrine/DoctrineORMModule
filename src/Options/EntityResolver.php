<?php

declare(strict_types=1);

namespace DoctrineORMModule\Options;

use InvalidArgumentException;
use Laminas\Stdlib\AbstractOptions;

use function class_exists;
use function sprintf;

/**
 * @template-extends AbstractOptions<mixed>
 */
final class EntityResolver extends AbstractOptions
{
    /**
     * Set the configuration key for the EventManager. Event manager key
     * is assembled as "doctrine.eventmanager.{key}" and pulled from
     * service locator.
     */
    protected string $eventManager = 'orm_default';

    /**
     * An array that maps a class name (or interface name) to another class
     * name
     *
     * @var mixed[]
     */
    protected array $resolvers = [];

    public function setEventManager(string $eventManager): self
    {
        $this->eventManager = $eventManager;

        return $this;
    }

    public function getEventManager(): string
    {
        return 'doctrine.eventmanager.' . $this->eventManager;
    }

    /**
     * @param  mixed[] $resolvers
     *
     * @throws InvalidArgumentException
     */
    public function setResolvers(array $resolvers): void
    {
        foreach ($resolvers as $old => $new) {
            if (! class_exists($new)) {
                throw new InvalidArgumentException(
                    sprintf(
                        '%s is resolved to the entity %s, which does not exist',
                        $old,
                        $new
                    )
                );
            }

            $this->resolvers[$old] = $new;
        }
    }

    /**
     * @return mixed[]
     */
    public function getResolvers(): array
    {
        return $this->resolvers;
    }
}
