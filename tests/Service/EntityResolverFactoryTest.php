<?php

declare(strict_types=1);

namespace DoctrineORMModuleTest\Service;

use Doctrine\ORM\Events;
use DoctrineORMModuleTest\Assets\Entity\ResolveTarget;
use DoctrineORMModuleTest\Assets\Entity\TargetEntity;
use DoctrineORMModuleTest\Framework\TestCase;

class EntityResolverFactoryTest extends TestCase
{
    public function testCanResolveTargetEntity(): void
    {
        $em            = $this->getEntityManager();
        $classMetadata = $em->getClassMetadata(ResolveTarget::class);
        $meta          = $classMetadata->associationMappings;

        $this->assertSame(TargetEntity::class, $meta['target']['targetEntity']);
    }

    public function testAssertSubscriberIsAdded(): void
    {
        $evm = $this->getEntityManager()->getEventManager();

        $this->assertTrue($evm->hasListeners(Events::loadClassMetadata));
        $this->assertTrue($evm->hasListeners(Events::onClassMetadataNotFound));
    }
}
