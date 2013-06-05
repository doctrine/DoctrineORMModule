<?php

namespace DoctrineORMModuleTest\Form;

use ArrayObject;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\ManyToMany;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder;
use DoctrineORMModule\Form\Annotation\ElementAnnotationsListener;
use DoctrineORMModuleTest\Assets\Entity\ResolveTarget;
use DoctrineORMModuleTest\Framework\TestCase;
use PHPUnit_Framework_TestCase;
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

    public function testToOneMergesOptions()
    {
        $listener = $this->getListener();
        $builder  = $listener->getBuilder();

        // binds the entity to the form
        $builder->getFormSpecification(new ResolveTarget());

        $event      = new Event();
        $annotation = new ManyToOne();
        $annotation->targetEntity = 'DoctrineORMModuleTest\Assets\Entity\ResolveTarget';

        $event->setParam('annotation', $annotation);
        $event->setParam('name', 'target');
        $event->setParam(
            'elementSpec',
            new ArrayObject(array('spec' => array('type' => 'overridden', 'options' => array('foo' => 'bar'))))
        );

        $expectedOptions = array(
            'foo'            => 'bar',
            'object_manager' => $this->getEntityManager(),
            'target_class'   => 'DoctrineORMModuleTest\Assets\Entity\TargetEntity'
        );

        $listener->handleToOneAnnotation($event);

        $spec = $event->getParam('elementSpec');
        $this->assertEquals('DoctrineORMModule\Form\Element\EntitySelect', $spec['spec']['type']);
        $this->assertEquals($expectedOptions, $spec['spec']['options']);
    }

    public function testToManyMergesOptionsAndOverridesMultipleAttribute()
    {
        $listener = $this->getListener();
        $builder  = $listener->getBuilder();

        // binds the entity to the form
        $builder->getFormSpecification(new ResolveTarget());

        $event      = new Event();
        $annotation = new ManyToMany();
        $annotation->targetEntity = 'DoctrineORMModuleTest\Assets\Entity\ResolveTarget';

        $event->setParam('annotation', $annotation);
        $event->setParam('name', 'target');
        $event->setParam(
            'elementSpec',
            new ArrayObject(array(
                'spec' => array(
                    'type' => 'overridden',
                    'options' => array('foo' => 'bar'),
                    'attributes' => array(
                        'multiple' => false
                    )
                )
            ))
        );

        $expectedOptions = array(
            'foo'            => 'bar',
            'object_manager' => $this->getEntityManager(),
            'target_class'   => 'DoctrineORMModuleTest\Assets\Entity\TargetEntity'
        );

        $listener->handleToManyAnnotation($event);

        $spec = $event->getParam('elementSpec');
        $this->assertEquals('DoctrineORMModule\Form\Element\EntitySelect', $spec['spec']['type']);
        $this->assertEquals($expectedOptions, $spec['spec']['options']);
        $this->assertTrue($spec['spec']['attributes']['multiple']);
    }
    
    public function testToOneReturnsEntitySelect()
    {
        $listener = $this->getListener();
        $builder  = $listener->getBuilder();

        // binds the entity to the form
        $builder->getFormSpecification(new ResolveTarget());

        $event      = new Event();
        $annotation = new ManyToOne();
        $annotation->targetEntity = 'DoctrineORMModuleTest\Assets\Entity\ResolveTarget';

        $event->setParam('annotation', $annotation);
        $event->setParam('name', 'target');
        $event->setParam(
            'elementSpec',
            new ArrayObject(array('spec' => array('attributes' => array('class' => 'foo'))))
        );

        $listener->handleToOneAnnotation($event);
        $spec = $event->getParam('elementSpec');
        $this->assertArrayNotHasKey('multiple', $spec['spec']['attributes']);
        $this->assertEquals('DoctrineORMModule\Form\Element\EntitySelect', $spec['spec']['type']);
        $this->assertEquals('DoctrineORMModuleTest\Assets\Entity\TargetEntity', $spec['spec']['options']['target_class']);
    }

    public function testToManyReturnsEntitySelect()
    {
        $listener = $this->getListener();
        $builder  = $listener->getBuilder();

        // binds the entity to the form
        $builder->getFormSpecification(new ResolveTarget());

        $event      = new Event();
        $annotation = new ManyToMany();
        $annotation->targetEntity = 'DoctrineORMModuleTest\Assets\Entity\ResolveTarget';

        $event->setParam('annotation', $annotation);
        $event->setParam('name', 'target');
        $event->setParam('elementSpec', new ArrayObject());

        $listener->handleToManyAnnotation($event);
        $spec = $event->getParam('elementSpec');
        $this->assertTrue($spec['spec']['attributes']['multiple']);
        $this->assertEquals('DoctrineORMModule\Form\Element\EntitySelect', $spec['spec']['type']);
        $this->assertEquals('DoctrineORMModuleTest\Assets\Entity\TargetEntity', $spec['spec']['options']['target_class']);
    }

    public function testHandleAnnotationAttributesShallAppend()
    {
        $listener         = $this->getListener();
        $event            = new Event();
        $annotation       = new Column();
        $annotation->type = 'text';

        $event->setParam('annotation', $annotation);
        $event->setParam(
            'elementSpec',
            new ArrayObject(array('spec' => array('attributes' => array('attr1' => 'value'))))
        );

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

    protected function getListener()
    {
        $builder  = new AnnotationBuilder($this->getEntityManager());
        $listener = new ElementAnnotationsListener($builder);

        return $listener;
    }
}
