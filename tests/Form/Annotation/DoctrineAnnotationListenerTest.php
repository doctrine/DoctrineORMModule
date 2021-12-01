<?php

declare(strict_types=1);

namespace DoctrineORMModuleTest\Form\Annotation;

use ArrayObject;
use DoctrineORMModule\Form\Annotation\DoctrineAnnotationListener;
use DoctrineORMModule\Form\Element\EntitySelect;
use DoctrineORMModuleTest\Assets\Entity\FormEntity;
use DoctrineORMModuleTest\Assets\Entity\FormEntityTarget;
use DoctrineORMModuleTest\Assets\Entity\TargetEntity;
use DoctrineORMModuleTest\Framework\TestCase;
use Laminas\EventManager\Event;
use Laminas\Form\Element;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Date;
use Laminas\Form\Element\DateTimeLocal;
use Laminas\Form\Element\Number;
use Laminas\Form\Element\Textarea;
use Laminas\Form\Element\Time;

class DoctrineAnnotationListenerTest extends TestCase
{
    /** @var DoctrineAnnotationListener */
    protected $listener;

    public function setUp(): void
    {
        $this->listener = new DoctrineAnnotationListener($this->getEntityManager());
    }

    /**
     * @dataProvider eventNameProvider
     */
    public function testEventsWithNoMetadata(string $method): void
    {
        $event = $this->getMetadataEvent();
        $this->listener->{$method}($event);

        $this->addToAssertionCount(1);
    }

    public function testToOne(): void
    {
        $listener = $this->listener;
        $event    = $this->getMetadataEvent();

        $event->setParam('name', 'targetOne');
        $listener->handleToOne($event);

        $elementSpec = $event->getParam('elementSpec');
        $this->assertEquals($this->getEntityManager(), $elementSpec['spec']['options']['object_manager']);
        $this->assertEquals(
            TargetEntity::class,
            $elementSpec['spec']['options']['target_class']
        );
        $this->assertEquals(EntitySelect::class, $elementSpec['spec']['type']);
    }

    public function testToOneMergesOptions(): void
    {
        $listener                              = $this->listener;
        $event                                 = $this->getMetadataEvent();
        $elementSpec                           = new ArrayObject();
        $elementSpec['spec']['options']['foo'] = 'bar';

        $event->setParam('name', 'targetOne');
        $event->setParam('elementSpec', $elementSpec);
        $listener->handleToOne($event);

        $this->assertEquals('bar', $elementSpec['spec']['options']['foo']);
        $this->assertCount(3, $elementSpec['spec']['options']);
    }

    public function testToOneHasNoEffectOnToMany(): void
    {
        $listener = $this->listener;
        $event    = $this->getMetadataEvent();

        $event->setParam('name', 'targetMany');
        $listener->handleToOne($event);

        $this->assertNull($event->getParam('elementSpec'));
        $this->assertNull($event->getParam('inputSpec'));
    }

    public function testToManyHasNoEffectOnToOne(): void
    {
        $listener = $this->listener;
        $event    = $this->getMetadataEvent();

        $event->setParam('name', 'targetOne');
        $listener->handleToMany($event);

        $this->assertNull($event->getParam('elementSpec'));
        $this->assertNull($event->getParam('inputSpec'));
    }

    public function testToMany(): void
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
            FormEntityTarget::class,
            $elementSpec['spec']['options']['target_class']
        );
        $this->assertEquals(EntitySelect::class, $elementSpec['spec']['type']);
        $this->assertFalse($inputSpec['required']);
    }

    public function testToManyMergesOptions(): void
    {
        $listener                              = $this->listener;
        $event                                 = $this->getMetadataEvent();
        $elementSpec                           = new ArrayObject();
        $elementSpec['spec']['options']['foo'] = 'bar';

        $event->setParam('name', 'targetMany');
        $event->setParam('elementSpec', $elementSpec);
        $listener->handleToMany($event);

        $this->assertEquals('bar', $elementSpec['spec']['options']['foo']);
        $this->assertCount(3, $elementSpec['spec']['options']);
    }

    public function testHandleExcludeAssociation(): void
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
    public function testHandleFilterField(string $name, string $type): void
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

    public function testHandlRequiredAssociation(): void
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

    public function testHandlRequiredAssociationSetsNullOption(): void
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

    public function testHandleRequiredField(): void
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

    public function testHandleRequiredFieldNonFieldProperty(): void
    {
        $listener = $this->listener;
        $event    = $this->getMetadataEvent();
        $event->setParam('name', 'targetMany');
        $listener->handleRequiredField($event);

        $inputSpec = $event->getParam('inputSpec');
        $this->assertFalse(isset($inputSpec['required']));
    }

    /**
     * @dataProvider eventTypeProvider
     */
    public function testHandleTypeField(string $name, string $type): void
    {
        $listener = $this->listener;
        $event    = $this->getMetadataEvent();

        $listener->handleFilterField($event);
        $this->assertNull($event->getParam('elementSpec'));

        $event->setParam('name', $name);
        $listener->handleTypeField($event);

        $elementSpec = $event->getParam('elementSpec');
        $this->assertInstanceOf(ArrayObject::class, $elementSpec);
        $this->assertEquals($type, $elementSpec['spec']['type']);
    }

    /**
     * @dataProvider eventValidatorProvider
     */
    public function testHandleValidatorField(string $name, ?string $type): void
    {
        $listener = $this->listener;
        $event    = $this->getMetadataEvent();

        $listener->handleFilterField($event);
        $this->assertNull($event->getParam('inputSpec'));

        $event->setParam('name', $name);
        $listener->handleValidatorField($event);

        $inputSpec = $event->getParam('inputSpec');
        $this->assertInstanceOf(ArrayObject::class, $inputSpec);

        if ($type === null) {
            $this->assertEmpty($inputSpec['validators']);
        } else {
            $this->assertEquals($type, $inputSpec['validators'][0]['name']);
        }
    }

    /**
     * @return list<array{string, string|null}>
     */
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

    /**
     * @return list<array{string, string}>
     */
    public function eventFilterProvider()
    {
        return [
            ['bool', 'Boolean'],
            ['boolean', 'Boolean'],
            ['bigint', 'Int'],
            ['integer', 'Int'],
            ['smallint', 'Int'],
            ['datetime', 'StringTrim'],
            ['datetimeImmutable', 'StringTrim'],
            ['datetimetz', 'StringTrim'],
            ['datetimetzImmutable', 'StringTrim'],
            ['date', 'StringTrim'],
            ['time', 'StringTrim'],
            ['string', 'StringTrim'],
            ['text', 'StringTrim'],
        ];
    }

    /**
     * @return list<array{string, class-string}>
     */
    public function eventTypeProvider()
    {
        return [
            ['bool', Checkbox::class],
            ['boolean', Checkbox::class],
            ['bigint', Number::class],
            ['integer', Number::class],
            ['smallint', Number::class],
            ['datetime', DateTimeLocal::class],
            ['datetimeImmutable', DateTimeLocal::class],
            ['datetimetz', DateTimeLocal::class],
            ['datetimetzImmutable', DateTimeLocal::class],
            ['date', Date::class],
            ['time', Time::class],
            ['string', Element::class],
            ['text', Textarea::class],
        ];
    }

    /**
     * @return list<array{string}>
     */
    public function eventNameProvider(): array
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

    protected function getMetadataEvent(): Event
    {
        $event    = new Event();
        $metadata = $this->getEntityManager()->getClassMetadata(FormEntity::class);
        $event->setParam('metadata', $metadata);

        return $event;
    }
}
