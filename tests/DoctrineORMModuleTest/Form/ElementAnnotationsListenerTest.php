<?php

use DoctrineORMModule\Form\Annotation\ElementAnnotationsListener;

/**
 * Description of ElementAnnotationsListenerTest
 *
 * @author otterdijk
 */
class ElementAnnotationsListenerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider eventProvider
     */
    public function testHandleAnnotationType($type, $expectedType)
    {
        $listener = new ElementAnnotationsListener();
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

    public function testHandleAnnotationAttributesShallAppent()
    {
        $listener = new ElementAnnotationsListener();
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
