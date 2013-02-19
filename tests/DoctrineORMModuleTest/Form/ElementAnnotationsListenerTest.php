<?php

use DoctrineORMModule\Form\Annotation\ElementAnnotationsListener;

/**
 * Description of ElementAnnotationsListenerTest
 *
 * @author otterdijk
 */
class ElementAnnotationsListenerTest extends PHPUnit_Framework_TestCase
{
    protected $listener;

    protected $entityManager;

    protected function setUp()
    {
        $this->entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->listener = new ElementAnnotationsListener($this->entityManager);
    }

    /**
     * @dataProvider eventProvider
     */
    public function testHandleAnnotationType($type, $expectedType)
    {
        $listener = $this->listener;
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
        $listener = $this->listener;
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

    /**
     * @covers DoctrineORMModule\Form\Annotation\ElemenetAnnotationsListener::handleLinkedFormElements
     * @dataProvider linkedElementTypeProvider
     */
    public function testHandleLinkedFormElements($type, $requiresEntityManager)
    {
        $listener = $this->listener;
        $event = new \Zend\EventManager\Event();
        $annotation = new \Zend\Form\Annotation\Type(array('value' => $type));

        $event->setParam('annotation', $annotation);
        $event->setParam('elementSpec', new ArrayObject(array(
            'spec' => array('options' => array()),
        )));

        $listener->handleLinkedFormElements($event);
        $spec = $event->getParam('elementSpec');

        if ($requiresEntityManager) {
            $this->assertArrayHasKey('object_manager', $spec['spec']['options']);
            $this->assertEquals($this->entityManager, $spec['spec']['options']['object_manager']);
            return;
        }

        $this->assertArrayNotHasKey('object_manager', $spec['spec']['options']);

    }

    public function linkedElementTypeProvider()
    {
        return array(
            array('DoctrineORMModule\Form\Element\EntityMultiCheckbox', true),
            array('DoctrineORMModule\Form\Element\EntityRadio',         true),
            array('DoctrineORMModule\Form\Element\EntitySelect',        true),
            array('Zend\Form\Element',                                  false),
        );
    }
}
