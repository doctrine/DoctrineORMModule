<?php

use DoctrineORMModule\Form\Annotation\ElementAnnotationsListener;
use DoctrineORMModuleTest\Framework\TestCase;

/**
 * Description of ElementAnnotationsListenerTest
 *
 * @author otterdijk
 */
class ElementAnnotationsListenerTest extends TestCase
{

    /**
     * @dataProvider eventProvider
     */
    public function testHandleAnnotationType($type, $expectedType)
    {
        $listener = new ElementAnnotationsListener($this->getEntityManager());
        $event = new Zend\EventManager\Event();
        $checkboxAnnotation = new Doctrine\ORM\Mapping\Column();
        $checkboxAnnotation->type = $type;
        $event->setParam('annotation', $checkboxAnnotation);
        $event->setParam('elementSpec', new ArrayObject(array(
            'spec' => array(),
        )));
        $listener->handleTypeAnnotation($event);
        $spec = $event->getParam('elementSpec');
        $this->assertEquals($expectedType, $spec['spec']['type']);
    }
    
    public function testToOneReturnsEntitySelect()
    {
        $listener   = new ElementAnnotationsListener($this->getEntityManager());
        $event      = new Zend\EventManager\Event();
        $annotation = new Doctrine\ORM\Mapping\ManyToOne();
        $annotation->targetEntity = 'DoctrineORMModuleTest\Assets\Entity\Category';

        $event->setParam('annotation', $annotation);
        $event->setParam('elementSpec', new ArrayObject(array(
            'spec' => array(
                'attributes' => array(
                    'class' => 'foo'
                )
            )
        )));

        $listener->handleToOneAnnotation($event);
        $spec = $event->getParam('elementSpec');
        $this->assertArrayNotHasKey('multiple', $spec['spec']['attributes']);
        $this->assertEquals('DoctrineORMModule\Form\Element\EntitySelect', $spec['spec']['type']);
        $this->assertEquals('DoctrineORMModuleTest\Assets\Entity\Category', $spec['spec']['options']['target_class']);
    }
    
    public function testToManyReturnsEntitySelect()
    {
        $listener   = new ElementAnnotationsListener($this->getEntityManager());
        $event      = new Zend\EventManager\Event();
        $annotation = new Doctrine\ORM\Mapping\ManyToMany();
        $annotation->targetEntity = 'DoctrineORMModuleTest\Assets\Entity\Category';

        $event->setParam('annotation', $annotation);
        $event->setParam('elementSpec', new ArrayObject());

        $listener->handleToManyAnnotation($event);
        $spec = $event->getParam('elementSpec');
        $this->assertArrayHasKey('multiple', $spec['spec']['attributes']);
        $this->assertEquals('DoctrineORMModule\Form\Element\EntitySelect', $spec['spec']['type']);
        $this->assertEquals('DoctrineORMModuleTest\Assets\Entity\Category', $spec['spec']['options']['target_class']);
    }

    public function testHandleAnnotationAttributesShallAppent()
    {
        $listener = new ElementAnnotationsListener($this->getEntityManager());
        $event = new Zend\EventManager\Event();
        $annotation = new Doctrine\ORM\Mapping\Column();

        $annotation->type = 'text';
        $event->setParam('annotation', $annotation);
        $event->setParam('elementSpec', new ArrayObject(array(
            'spec' => array('attributes' => array('attr1' => 'value')),
        )));

        $listener->handleAttributesAnnotation($event);
        $spec = $event->getParam('elementSpec');
        $this->assertCount(2, $spec['spec']['attributes']);
        $this->assertArrayHasKey('attr1', $spec['spec']['attributes']);
        $this->assertEquals('textarea', $spec['spec']['attributes']['type']);
        $this->assertEquals('value', $spec['spec']['attributes']['attr1']);
    }

    public function eventProvider()
    {
        return array(
            array('bool', 'Zend\Form\Element\Checkbox'),
            array('boolean', 'Zend\Form\Element\Checkbox'),
            array('string', 'Zend\Form\Element'),
        );
    }
}
