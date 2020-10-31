<?php

namespace DoctrineORMModuleTest\Service;

use Doctrine\ORM\EntityManager;
use DoctrineORMModule\Service\FormAnnotationBuilderFactory;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

/**
 * Tests for {@see \DoctrineORMModule\Service\FormAnnotationBuilderFactory}
 *
 * @covers \DoctrineORMModule\Service\FormAnnotationBuilderFactory
 */
class FormAnnotationBuilderFactoryTest extends TestCase
{
    /**
     * @group #352
     */
    public function testFormElementManagerGetsInjected(): void
    {
        $entityManager      = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $formElementManager = $this->getMockBuilder(FormElementManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $serviceManager = new ServiceManager();

        $serviceManager->setService('doctrine.entitymanager.test', $entityManager);
        $serviceManager->setService('FormElementManager', $formElementManager);

        $annotationBuilderFactory = new FormAnnotationBuilderFactory('test');
        $annotationBuilder        = $annotationBuilderFactory->createService($serviceManager);

        $this->assertSame($formElementManager, $annotationBuilder->getFormFactory()->getFormElementManager());
    }
}
