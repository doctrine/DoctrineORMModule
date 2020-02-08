<?php

namespace DoctrineORMModuleTest\Form;

use ArrayObject;
use DoctrineORMModule\Form\Annotation\ElementAnnotationsListener;
use DoctrineORMModuleTest\Framework\TestCase;
use Laminas\EventManager\Event;

class ElementAnnotationsListenerTest extends TestCase
{
    /**
     * @var ElementAnnotationsListener
     */
    protected $listener;

    public function setUp() : void
    {
        $this->listener = new ElementAnnotationsListener($this->getEntityManager());
    }

    /**
     * @dataProvider eventNameProvider
     */
    public function testEventsWithNoMetadata($method)
    {
        $event = $this->getMetadataEvent();
        $this->listener->{$method}($event);

        $this->addToAssertionCount(1);
    }

    public function testToOne()
    {
        $listener = $this->listener;
        $event    = $this->getMetadataEvent();

        $event->setParam('name', 'targetOne');
        $listener->handleToOne($event);

        $elementSpec = $event->getParam('elementSpec');
        $this->assertEquals($this->getEntityManager(), $elementSpec['spec']['options']['object_manager']);
        $this->assertEquals(
            \DoctrineORMModuleTest\Assets\Entity\TargetEntity::class,
            $elementSpec['spec']['options']['target_class']
        );
        $this->assertEquals(\DoctrineORMModule\Form\Element\EntitySelect::class, $elementSpec['spec']['type']);
    }

    public function testToOneMergesOptions()
    {
        $listener    = $this->listener;
        $event       = $this->getMetadataEvent();
        $elementSpec = new ArrayObject();
        $elementSpec['spec']['options']['foo'] = 'bar';

        $event->setParam('name', 'targetOne');
        $event->setParam('elementSpec', $elementSpec);
        $listener->handleToOne($event);

        $this->assertEquals('bar', $elementSpec['spec']['options']['foo']);
        $this->assertCount(3, $elementSpec['spec']['options']);
    }

    public function testToOneHasNoEffectOnToMany()
    {
        $listener = $this->listener;
        $event    = $this->getMetadataEvent();

        $event->setParam('name', 'targetMany');
        $listener->handleToOne($event);

        $this->assertNull($event->getParam('elementSpec'));
        $this->assertNull($event->getParam('inputSpec'));
    }

    public function testToManyHasNoEffectOnToOne()
    {
        $listener = $this->listener;
        $event    = $this->getMetadataEvent();

        $event->setParam('name', 'targetOne');
        $listener->handleToMany($event);

        $this->assertNull($event->getParam('elementSpec'));
        $this->assertNull($event->getParam('inputSpec'));
    }

    public function testToMany()
    {
        $listener = $this->listener;
        $event    = $this->getMetadataEvent();

        $event->setParam('name', 'targetMany');
        $listener->handleToMany($event);

        $elementSpec = $event->getParam('elementSpec');
        $inputSpec   = $event->getParam('inputSpec');

        $this->assertTrue($elementSpec['spec']['attributes']['multiple']);
        $this->assertEquals($this->getEntityManager(), $elementSpec['spec']['options']['object_manager']);
        $this->assertEquals(
            \DoctrineORMModuleTest\Assets\Entity\FormEntityTarget::class,
            $elementSpec['spec']['options']['target_class']
        );
        $this->assertEquals(\DoctrineORMModule\Form\Element\EntitySelect::class, $elementSpec['spec']['type']);
        $this->assertFalse($inputSpec['required']);
    }

    public function testToManyMergesOptions()
    {
        $listener    = $this->listener;
        $event       = $this->getMetadataEvent();
        $elementSpec = new ArrayObject();
        $elementSpec['spec']['options']['foo'] = 'bar';

        $event->setParam('name', 'targetMany');
        $event->setParam('elementSpec', $elementSpec);
        $listener->handleToMany($event);

        $this->assertEquals('bar', $elementSpec['spec']['options']['foo']);
        $this->assertCount(3, $elementSpec['spec']['options']);
    }

    public function testHandleExcludeAssociation()
    {
        $listener = $this->listener;
        $event    = $this->getMetadataEvent();
        $event->setParam('name', 'targetMany');

        $this->assertTrue($listener->handleExcludeAssociation($event));

        $event->setParam('name', 'targetOne');
        $this->assertFalse($listener->handleExcludeAssociation($event));
    }

    /**
     * @dataProvider eventFilterProvider
     */
    public function testHandleFilterField($name, $type)
    {
        $listener = $this->listener;
        $event    = $this->getMetadataEvent();

        $listener->handleFilterField($event);
        $this->assertNull($event->getParam('inputSpec'));

        $event->setParam('name', $name);
        $listener->handleFilterField($event);

        $inputSpec = $event->getParam('inputSpec');
        $this->assertEquals($type, $inputSpec['filters'][0]['name']);
    }

    public function testHandlRequiredAssociation()
    {
        $listener = $this->listener;
        $event    = $this->getMetadataEvent();

        $listener->handleRequiredAssociation($event);
        $this->assertNull($event->getParam('inputSpec'));

        $event->setParam('name', 'targetMany');
        $listener->handleRequiredAssociation($event);

        $inputSpec = $event->getParam('inputSpec');
        $this->assertFalse($inputSpec['required']);
    }

    public function testHandlRequiredAssociationSetsNullOption()
    {
        $listener = $this->listener;
        $event    = $this->getMetadataEvent();
        $event->setParam('name', 'targetOneNullable');
        $listener->handleRequiredAssociation($event);

        $elementSpec = $event->getParam('elementSpec');

        $this->assertEquals('NULL', $elementSpec['spec']['options']['empty_option']);

        $listener->handleRequiredAssociation($event);
        $elementSpec['spec']['options']['empty_option'] = 'foo';

        $this->assertEquals('foo', $elementSpec['spec']['options']['empty_option']);
    }

    public function testHandleRequiredField()
    {
        $listener = $this->listener;
        $event    = $this->getMetadataEvent();

        $event->setParam('name', 'datetime');
        $listener->handleRequiredField($event);
        $inputSpec = $event->getParam('inputSpec');
        $this->assertTrue($inputSpec['required']);

        $event->setParam('name', 'string');
        $listener->handleRequiredField($event);
        $this->assertTrue($inputSpec['required']);

        $event->setParam('name', 'stringNullable');
        $listener->handleRequiredField($event);
        $this->assertFalse($inputSpec['required']);
    }

    public function testHandleRequiredFieldNonFieldProperty()
    {
        $listener = $this->listener;
        $event    = $this->getMetadataEvent();
        $event->setParam('name', 'targetMany');
        $listener->handleRequiredField($event);
        $this->assertFalse(isset($inputSpec['required']));
    }

    /**
     * @dataProvider eventTypeProvider
     */
    public function testHandleTypeField($name, $type)
    {
        $listener = $this->listener;
        $event    = $this->getMetadataEvent();

        $listener->handleFilterField($event);
        $this->assertNull($event->getParam('elementSpec'));

        $event->setParam('name', $name);
        $listener->handleTypeField($event);

        $elementSpec = $event->getParam('elementSpec');
        $this->assertEquals($type, $elementSpec['spec']['type']);
    }

    /**
     * @dataProvider eventValidatorProvider
     */
    public function testHandlevalidatorField($name, $type)
    {
        $listener = $this->listener;
        $event    = $this->getMetadataEvent();

        $listener->handleFilterField($event);
        $this->assertNull($event->getParam('inputSpec'));

        $event->setParam('name', $name);
        $listener->handleValidatorField($event);

        $inputSpec = $event->getParam('inputSpec');
        if (null === $type) {
            $this->assertEmpty($inputSpec['validators']);
        } else {
            $this->assertEquals($type, $inputSpec['validators'][0]['name']);
        }
    }

    public function eventValidatorProvider()
    {
        return [
            ['bool', 'InArray'],
            ['boolean', 'InArray'],
            ['bigint', 'Int'],
            ['float', 'Float'],
            ['integer', 'Int'],
            ['smallint', 'Int'],
            ['datetime', null],
            ['datetimetz', null],
            ['date', null],
            ['time', null],
            ['string', 'StringLength'],
            ['stringNullable', null],
            ['text', null],
        ];
    }

    public function eventFilterProvider()
    {
        return [
            ['bool', 'Boolean'],
            ['boolean', 'Boolean'],
            ['bigint', 'Int'],
            ['integer', 'Int'],
            ['smallint', 'Int'],
            ['datetime', 'StringTrim'],
            ['datetimetz', 'StringTrim'],
            ['date', 'StringTrim'],
            ['time', 'StringTrim'],
            ['string', 'StringTrim'],
            ['text', 'StringTrim'],
        ];
    }

    public function eventTypeProvider()
    {
        return [
            ['bool', \Laminas\Form\Element\Checkbox::class],
            ['boolean', \Laminas\Form\Element\Checkbox::class],
            ['bigint', \Laminas\Form\Element\Number::class],
            ['integer', \Laminas\Form\Element\Number::class],
            ['smallint', \Laminas\Form\Element\Number::class],
            ['datetime', \Laminas\Form\Element\DateTime::class],
            ['datetimetz', \Laminas\Form\Element\DateTime::class],
            ['date', \Laminas\Form\Element\Date::class],
            ['time', \Laminas\Form\Element\Time::class],
            ['string', \Laminas\Form\Element::class],
            ['text', \Laminas\Form\Element\Textarea::class],
        ];
    }

    public function eventNameProvider()
    {
        return [
            [
                'handleFilterField',
                'handleTypeField',
                'handleValidatorField',
                'handleRequiredField',
                'handleRequiredAssociation',
                'handleExcludeField',
                'handleExcludeAssociation',
                'handleToOne',
                'handleToMany',
            ],
        ];
    }

    protected function getMetadataEvent()
    {
        $event    = new Event();
        $metadata = $this->getEntityManager()->getClassMetadata(\DoctrineORMModuleTest\Assets\Entity\FormEntity::class);
        $event->setParam('metadata', $metadata);

        return $event;
    }
}
