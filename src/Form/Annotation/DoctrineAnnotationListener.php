<?php

declare(strict_types=1);

namespace DoctrineORMModule\Form\Annotation;

use ArrayObject;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectManager;
use DoctrineORMModule\Form\Element\EntitySelect;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Form\Element as LaminasFormElement;

use function array_key_exists;
use function array_merge;
use function in_array;

class DoctrineAnnotationListener extends AbstractListenerAggregate
{
    protected ObjectManager $objectManager;

    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            EntityBasedFormBuilder::EVENT_CONFIGURE_FIELD,
            [$this, 'handleFilterField']
        );
        $this->listeners[] = $events->attach(
            EntityBasedFormBuilder::EVENT_CONFIGURE_FIELD,
            [$this, 'handleTypeField']
        );
        $this->listeners[] = $events->attach(
            EntityBasedFormBuilder::EVENT_CONFIGURE_FIELD,
            [$this, 'handleValidatorField']
        );
        $this->listeners[] = $events->attach(
            EntityBasedFormBuilder::EVENT_CONFIGURE_FIELD,
            [$this, 'handleRequiredField']
        );
        $this->listeners[] = $events->attach(
            EntityBasedFormBuilder::EVENT_EXCLUDE_FIELD,
            [$this, 'handleExcludeField']
        );
        $this->listeners[] = $events->attach(
            EntityBasedFormBuilder::EVENT_CONFIGURE_ASSOCIATION,
            [$this, 'handleToOne']
        );
        $this->listeners[] = $events->attach(
            EntityBasedFormBuilder::EVENT_CONFIGURE_ASSOCIATION,
            [$this, 'handleToMany']
        );
        $this->listeners[] = $events->attach(
            EntityBasedFormBuilder::EVENT_CONFIGURE_ASSOCIATION,
            [$this, 'handleRequiredAssociation']
        );
        $this->listeners[] = $events->attach(
            EntityBasedFormBuilder::EVENT_EXCLUDE_ASSOCIATION,
            [$this, 'handleExcludeAssociation']
        );
    }

    /**
     * @internal
     */
    public function handleToOne(EventInterface $event): void
    {
        $metadata = $event->getParam('metadata');
        $mapping  = $this->getAssociationMapping($event);
        if (! $mapping || ! $metadata->isSingleValuedAssociation($event->getParam('name'))) {
            return;
        }

        $this->prepareEvent($event);
        $this->mergeAssociationOptions($event->getParam('elementSpec'), $mapping['targetEntity']);
    }

    /**
     * @internal
     */
    public function handleToMany(EventInterface $event): void
    {
        $metadata = $event->getParam('metadata');
        $mapping  = $this->getAssociationMapping($event);
        if (! $mapping || ! $metadata->isCollectionValuedAssociation($event->getParam('name'))) {
            return;
        }

        $this->prepareEvent($event);

        $elementSpec           = $event->getParam('elementSpec');
        $inputSpec             = $event->getParam('inputSpec');
        $inputSpec['required'] = false;

        $this->mergeAssociationOptions($elementSpec, $mapping['targetEntity']);

        $elementSpec['spec']['attributes']['multiple'] = true;
    }

    /**
     * @internal
     */
    public function handleExcludeAssociation(EventInterface $event): bool
    {
        $metadata = $event->getParam('metadata');

        return $metadata && $metadata->isAssociationInverseSide($event->getParam('name'));
    }

    /**
     * @internal
     */
    public function handleExcludeField(EventInterface $event): bool
    {
        $metadata    = $event->getParam('metadata');
        $identifiers = $metadata->getIdentifierFieldNames();

        return in_array($event->getParam('name'), $identifiers) &&
            $metadata->generatorType === ClassMetadata::GENERATOR_TYPE_IDENTITY;
    }

    /**
     * @internal
     */
    public function handleFilterField(EventInterface $event): void
    {
        $metadata = $event->getParam('metadata');
        if (! $metadata || ! $metadata->hasField($event->getParam('name'))) {
            return;
        }

        $this->prepareEvent($event);

        $inputSpec = $event->getParam('inputSpec');

        switch ($metadata->getTypeOfField($event->getParam('name'))) {
            case 'bool':
            case 'boolean':
                $inputSpec['filters'][] = ['name' => 'Boolean'];
                break;
            case 'bigint':
            case 'integer':
            case 'smallint':
                $inputSpec['filters'][] = ['name' => 'Int'];
                break;
            case 'datetime':
            case 'datetime_immutable':
            case 'datetimetz':
            case 'datetimetz_immutable':
            case 'date':
            case 'time':
            case 'string':
            case 'text':
                $inputSpec['filters'][] = ['name' => 'StringTrim'];
                break;
        }
    }

    /**
     * @internal
     */
    public function handleRequiredAssociation(EventInterface $event): void
    {
        $metadata = $event->getParam('metadata');
        $mapping  = $this->getAssociationMapping($event);
        if (! $mapping) {
            return;
        }

        $this->prepareEvent($event);

        $inputSpec   = $event->getParam('inputSpec');
        $elementSpec = $event->getParam('elementSpec');

        if ($metadata->isCollectionValuedAssociation($event->getParam('name'))) {
            $inputSpec['required'] = false;
        } elseif (isset($mapping['joinColumns'])) {
            $required = true;
            foreach ($mapping['joinColumns'] as $joinColumn) {
                if (! isset($joinColumn['nullable']) || ! $joinColumn['nullable']) {
                    continue;
                }

                $required = false;
                if (
                    (isset($elementSpec['spec']['options']) &&
                        ! array_key_exists('empty_option', $elementSpec['spec']['options'])) ||
                    ! isset($elementSpec['spec']['options'])
                ) {
                    $elementSpec['spec']['options']['empty_option'] = 'NULL';
                }

                break;
            }

            $inputSpec['required'] = $required;
        }
    }

    /**
     * @internal
     */
    public function handleRequiredField(EventInterface $event): void
    {
        $this->prepareEvent($event);

        $metadata  = $event->getParam('metadata');
        $inputSpec = $event->getParam('inputSpec');

        if (! $metadata || ! $metadata->hasField($event->getParam('name'))) {
            return;
        }

        $inputSpec['required'] = ! $metadata->isNullable($event->getParam('name'));
    }

    /**
     * @internal
     */
    public function handleTypeField(EventInterface $event): void
    {
        $metadata = $event->getParam('metadata');
        $mapping  = $this->getFieldMapping($event);
        if (! $mapping) {
            return;
        }

        $this->prepareEvent($event);

        $elementSpec = $event->getParam('elementSpec');

        if (isset($elementSpec['spec']['options']['target_class'])) {
            $this->mergeAssociationOptions($elementSpec, $elementSpec['spec']['options']['target_class']);

            return;
        }

        if (isset($elementSpec['spec']['type']) || isset($elementSpec['spec']['attributes']['type'])) {
            return;
        }

        switch ($metadata->getTypeOfField($event->getParam('name'))) {
            case 'bigint':
            case 'integer':
            case 'smallint':
                $type = LaminasFormElement\Number::class;
                break;
            case 'bool':
            case 'boolean':
                $type = LaminasFormElement\Checkbox::class;
                break;
            case 'date':
                $type = LaminasFormElement\Date::class;
                break;
            case 'datetime':
            case 'datetime_immutable':
            case 'datetimetz':
            case 'datetimetz_immutable':
                $type = LaminasFormElement\DateTimeLocal::class;
                break;
            case 'time':
                $type = LaminasFormElement\Time::class;
                break;
            case 'text':
                $type = LaminasFormElement\Textarea::class;
                break;
            default:
                $type = LaminasFormElement::class;
                break;
        }

        $elementSpec['spec']['type'] = $type;
    }

    /**
     * @internal
     */
    public function handleValidatorField(EventInterface $event): void
    {
        $mapping = $this->getFieldMapping($event);
        if (! $mapping) {
            return;
        }

        $metadata = $event->getParam('metadata');

        $this->prepareEvent($event);

        $inputSpec = $event->getParam('inputSpec');

        switch ($metadata->getTypeOfField($event->getParam('name'))) {
            case 'bool':
            case 'boolean':
                $inputSpec['validators'][] = [
                    'name'    => 'InArray',
                    'options' => ['haystack' => ['0', '1']],
                ];
                break;
            case 'float':
                $inputSpec['validators'][] = ['name' => 'Float'];
                break;
            case 'bigint':
            case 'integer':
            case 'smallint':
                $inputSpec['validators'][] = ['name' => 'Int'];
                break;
            case 'string':
                $elementSpec = $event->getParam('elementSpec');
                if (
                    isset($elementSpec['spec']['type']) &&
                    in_array($elementSpec['spec']['type'], ['File', LaminasFormElement\File::class])
                ) {
                    return;
                }

                if (isset($mapping['length'])) {
                    $inputSpec['validators'][] = [
                        'name'    => 'StringLength',
                        'options' => ['max' => $mapping['length']],
                    ];
                }

                break;
        }
    }

    /**
     * @return mixed[]|null
     */
    protected function getFieldMapping(EventInterface $event): ?array
    {
        $metadata = $event->getParam('metadata');
        if ($metadata && $metadata->hasField($event->getParam('name'))) {
            return $metadata->getFieldMapping($event->getParam('name'));
        }

        return null;
    }

    /**
     * @return mixed[]|null
     */
    protected function getAssociationMapping(EventInterface $event): ?array
    {
        $metadata = $event->getParam('metadata');
        if ($metadata && $metadata->hasAssociation($event->getParam('name'))) {
            return $metadata->getAssociationMapping($event->getParam('name'));
        }

        return null;
    }

    protected function mergeAssociationOptions(ArrayObject $elementSpec, string $targetEntity): void
    {
        $options = $elementSpec['spec']['options'] ?? [];
        $options = array_merge(
            [
                'object_manager' => $this->objectManager,
                'target_class'   => $targetEntity,
            ],
            $options
        );

        $elementSpec['spec']['options'] = $options;
        if (isset($elementSpec['spec']['type'])) {
            return;
        }

        $elementSpec['spec']['type'] = EntitySelect::class;
    }

    /**
     * Normalizes event setting all expected parameters.
     */
    protected function prepareEvent(EventInterface $event): void
    {
        foreach (['elementSpec', 'inputSpec'] as $type) {
            if ($event->getParam($type)) {
                continue;
            }

            $event->setParam($type, new ArrayObject());
        }

        $elementSpec = $event->getParam('elementSpec');
        $inputSpec   = $event->getParam('inputSpec');

        if (! isset($elementSpec['spec'])) {
            $elementSpec['spec'] = [];
        }

        if (! isset($inputSpec['filters'])) {
            $inputSpec['filters'] = [];
        }

        if (isset($inputSpec['validators'])) {
            return;
        }

        $inputSpec['validators'] = [];
    }
}
