<?php

declare(strict_types=1);

namespace DoctrineORMModule\Stdlib\Hydrator;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineObjectHydrator;

/**
 * This hydrator is used as an optimization purpose for Doctrine ORM, and retrieves references to
 * objects instead of fetching the object from the database.
 *
 * @deprecated will be removed in 0.8.0, use DoctrineModule\Stdlib\Hydrator\DoctrineObject instead
 *
 * @link    http://www.doctrine-project.org/
 */
class DoctrineEntity extends DoctrineObjectHydrator
{
}
