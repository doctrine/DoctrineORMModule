<?php

namespace DoctrineORMModuleTest\Collector;

use DoctrineORMModuleTest\Framework\TestCase;

class DefaultRepositoryTest extends TestCase
{
    public function testEntityHasDefaultRepositoryInstance()
    {
        $entityWithDefaultRepository = \DoctrineORMModuleTest\Assets\Entity\EntityWithoutRepository::class;

        $repository = $this->getEntityManager()->getRepository($entityWithDefaultRepository);
        $this->assertInstanceOf(\DoctrineORMModuleTest\Assets\RepositoryClass::class, $repository);
    }
}
