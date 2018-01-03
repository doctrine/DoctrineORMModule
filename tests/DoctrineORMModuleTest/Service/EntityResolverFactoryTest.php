<?php

namespace DoctrineORMModuleTest\Service;

use Doctrine\ORM\Events;
use DoctrineORMModuleTest\Framework\TestCase as TestCase;

class EntityResolverFactoryTest extends TestCase
{
    public function testCanResolveTargetEntity()
    {
        $em            = $this->getEntityManager();
        $classMetadata = $em->getClassMetadata(\DoctrineORMModuleTest\Assets\Entity\ResolveTarget::class);
        $meta          = $classMetadata->associationMappings;

        $this->assertSame(\DoctrineORMModuleTest\Assets\Entity\TargetEntity::class, $meta['target']['targetEntity']);
    }

    public function testAssertSubscriberIsAdded()
    {
        $evm = $this->getEntityManager()->getEventManager();

        $this->assertTrue($evm->hasListeners(Events::loadClassMetadata));
        $this->assertTrue($evm->hasListeners(Events::onClassMetadataNotFound));
    }
}
