<?php

namespace DoctrineORMModule\Stdlib\Hydrator;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineObjectHydrator;

/**
 * This hydrator is used as an optimization purpose for Doctrine ORM, and retrieves references to
 * objects instead of fetching the object from the database.
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @since   0.5.0
 * @author  Michaël Gallego <mic.gallego@gmail.com>
 *
 * @deprecated will be removed in 0.8.0, use DoctrineModule\Stdlib\Hydrator\DoctrineObject instead
 */
class DoctrineEntity extends DoctrineObjectHydrator
{
}
