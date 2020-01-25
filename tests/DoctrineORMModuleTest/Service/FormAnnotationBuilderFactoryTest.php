<?php

namespace DoctrineORMModuleTest\Service;

use DoctrineORMModule\Service\FormAnnotationBuilderFactory;
use PHPUnit\Framework\TestCase;
use Laminas\ServiceManager\ServiceManager;

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
    public function testFormElementManagerGetsInjected()
    {
        $entityManager      = $this->getMockBuilder(\Doctrine\ORM\EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $formElementManager = $this->getMockBuilder(\Laminas\Form\FormElementManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $serviceManager = new ServiceManager();

        $serviceManager->setService('doctrine.entitymanager.test', $entityManager);
        $serviceManager->setService('FormElementManager', $formElementManager);

        $annotationBuilderFactory = new FormAnnotationBuilderFactory('test');
        $annotationBuilder = $annotationBuilderFactory->createService($serviceManager);

        $this->assertSame($formElementManager, $annotationBuilder->getFormFactory()->getFormElementManager());
    }
}
