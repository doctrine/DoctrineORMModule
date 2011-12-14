<?php
namespace SpiffyDoctrineORM\Factory;
use Doctrine\Common\Annotations\AnnotationRegistry,
    Doctrine\ORM\EntityManager as DoctrineEntityManager,
	SpiffyDoctrineORM\Doctrine\ORM\Connection;

class EntityManager
{
	public static function get(Connection $conn)
	{
		return DoctrineEntityManager::create(
			$conn->getInstance(),
			$conn->getInstance()->getConfiguration(),
			$conn->getInstance()->getEventManager()
		);
	}
}