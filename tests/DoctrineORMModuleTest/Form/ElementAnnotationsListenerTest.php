<?php

namespace DoctrineORMModuleTest\Form;

use ArrayObject;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\OneToMany;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder;
use DoctrineORMModule\Form\Annotation\ElementAnnotationsListener;
use DoctrineORMModuleTest\Assets\Entity\FormEntity;
use DoctrineORMModuleTest\Framework\TestCase;
use Zend\EventManager\Event;

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
        $listener                 = $this->getListener();
        $event                    = new Event();
        $checkboxAnnotation       = new Column();
        $checkboxAnnotation->type = $type;

        $event->setParam('annotation', $checkboxAnnotation);
        $event->setParam('elementSpec', new ArrayObject(array('spec' => array())));
        $listener->handleTypeAnnotation($event);

        $spec = $event->getParam('elementSpec');

        $this->assertEquals($expectedType, $spec['spec']['type']);
    }

    public function testOneWithNullableOption()
    {
        $listener = $this->getListener();
        $event    = $this->getOneEvent();
        $listener->handleToOneAnnotation($event);

        $elementSpec = $event->getParam('elementSpec');
        $inputSpec   = $event->getParam('inputSpec');

        $this->assertArrayNotHasKey('empty_option', $elementSpec['spec']['options']);

        $event->setParam('name', 'targetOneNullable');
        $listener->handleToOneAnnotation($event);

        $this->assertArrayHasKey('empty_option', $elementSpec['spec']['options']);
        $this->assertEquals('NULL', $elementSpec['spec']['options']['empty_option']);
        $this->assertArrayHasKey('required', $inputSpec);
        $this->assertFalse($inputSpec['required']);

        $elementSpec['spec']['options']['empty_option'] = 'pitydafoo';
        $listener->handleToOneAnnotation($event);

        $this->assertEquals('pitydafoo', $elementSpec['spec']['options']['empty_option']);
    }

    public function testManyNotRequired()
    {
        $listener = $this->getListener();
        $event    = $this->getManyEvent();

        $inputSpec = $event->getParam('inputSpec');

        $listener->handleToManyAnnotation($event);

        $this->assertArrayHasKey('required', $inputSpec);
        $this->assertFalse($inputSpec['required']);
    }

    public function testManyType()
    {
        $listener = $this->getListener();
        $event    = $this->getManyEvent();
        $listener->handleToManyAnnotation($event);

        $spec = $event->getParam('elementSpec');

        $this->assertEquals('DoctrineORMModule\Form\Element\EntitySelect', $spec['spec']['type']);
        $this->assertTrue($spec['spec']['attributes']['multiple']);
    }

    public function testOneType()
    {
        $listener = $this->getListener();
        $event    = $this->getOneEvent();
        $listener->handleToOneAnnotation($event);

        $spec = $event->getParam('elementSpec');

        $this->assertEquals('DoctrineORMModule\Form\Element\EntitySelect', $spec['spec']['type']);
    }

    public function testOptionsAreMerged()
    {
        $listener    = $this->getListener();
        $event       = $this->getManyEvent();
        $elementSpec = $event->getParam('elementSpec');
        $elementSpec['spec']['options']['foo'] = 'bar';

        $listener->handleToManyAnnotation($event);

        $spec = $event->getParam('elementSpec');
        $this->assertArrayHasKey('foo', $spec['spec']['options']);
        $this->assertEquals('bar', $spec['spec']['options']['foo']);
        $this->assertCount(3, $spec['spec']['options']);

        $listener    = $this->getListener();
        $event       = $this->getOneEvent();
        $elementSpec = $event->getParam('elementSpec');
        $elementSpec['spec']['options']['foo'] = 'bar';

        $listener->handleToOneAnnotation($event);

        $spec = $event->getParam('elementSpec');
        $this->assertArrayHasKey('foo', $spec['spec']['options']);
        $this->assertEquals('bar', $spec['spec']['options']['foo']);
        $this->assertCount(3, $spec['spec']['options']);
    }

    public function eventProvider()
    {
        return array(
            array('bool', 'Zend\Form\Element\Checkbox'),
            array('boolean', 'Zend\Form\Element\Checkbox'),
            array('string', 'Zend\Form\Element'),
        );
    }

    protected function getOneEvent()
    {
        $event                    = new Event();
        $annotation               = new ManyToOne();
        $annotation->targetEntity = 'DoctrineORMModuleTest\Assets\Entity\TargetEntity';

        $event->setParam('annotation', $annotation);
        $event->setParam('name', 'targetOne');
        $event->setParam('elementSpec', new ArrayObject());
        $event->setParam('inputSpec', new ArrayObject());

        return $event;
    }

    protected function getManyEvent()
    {
        $event                    = new Event();
        $annotation               = new OneToMany();
        $annotation->targetEntity = 'DoctrineORMModuleTest\Assets\Entity\FormEntityTarget';

        $event->setParam('annotation', $annotation);
        $event->setParam('name', 'targetMany');
        $event->setParam('elementSpec', new ArrayObject());
        $event->setParam('inputSpec', new ArrayObject());

        return $event;
    }

    protected function getListener()
    {
        $builder = new AnnotationBuilder($this->getEntityManager());
        $builder->getFormSpecification(new FormEntity());

        return new ElementAnnotationsListener($builder);
    }
}
