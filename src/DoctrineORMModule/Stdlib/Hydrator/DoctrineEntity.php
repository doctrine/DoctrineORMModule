<?php

namespace DoctrineORMModule\Stdlib\Hydrator;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineObjectHydrator;

/**
 * This hydrator is used as an optimization purpose for Doctrine ORM, and retrieves references to
 * objects instead of fetching the object from the database.
 */
class DoctrineEntity extends DoctrineObjectHydrator
{
    /**
     * @param  string    $target
     * @param  int|array $identifiers
     * @return object
     */
    protected function find($target, $identifiers)
    {
        return $this->objectManager->getReference($target, $identifiers);
    }
}