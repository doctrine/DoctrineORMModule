<?php
namespace DoctrineORMModule\Form\Annotation;

use ArrayObject;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use DoctrineModule\Form\Element;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;

class ElementAnnotationsListener extends AbstractListenerAggregate
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $objectManager;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(AnnotationBuilder::EVENT_CONFIGURE_FIELD, array($this, 'handleFilterField'));
        $this->listeners[] = $events->attach(AnnotationBuilder::EVENT_CONFIGURE_FIELD, array($this, 'handleTypeField'));
        $this->listeners[] = $events->attach(AnnotationBuilder::EVENT_CONFIGURE_FIELD, array($this, 'handleValidatorField'));
        $this->listeners[] = $events->attach(AnnotationBuilder::EVENT_CONFIGURE_FIELD, array($this, 'handleRequiredField'));
        $this->listeners[] = $events->attach(AnnotationBuilder::EVENT_EXCLUDE_FIELD, array($this, 'handleExcludeField'));

        $this->listeners[] = $events->attach(AnnotationBuilder::EVENT_CONFIGURE_ASSOCIATION, array($this, 'handleToOne'));
        $this->listeners[] = $events->attach(AnnotationBuilder::EVENT_CONFIGURE_ASSOCIATION, array($this, 'handleToMany'));
        $this->listeners[] = $events->attach(AnnotationBuilder::EVENT_CONFIGURE_ASSOCIATION, array($this, 'handleRequiredAssociation'));
        $this->listeners[] = $events->attach(AnnotationBuilder::EVENT_EXCLUDE_ASSOCIATION, array($this, 'handleExcludeAssociation'));
    }

    /**
     * @param EventInterface $event
     * @internal
     */
    public function handleToOne(EventInterface $event)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadata $metadata */
        $metadata = $event->getParam('metadata');
        $mapping  = $this->getAssociationMapping($event);
        if (!$mapping || !$metadata->isSingleValuedAssociation($event->getParam('name'))) {
            return;
        }

        $this->prepareEvent($event);
        $this->mergeAssociationOptions($event->getParam('elementSpec'), $mapping['targetEntity']);
    }

    /**
     * @param EventInterface $event
     * @internal
     */
    public function handleToMany(EventInterface $event)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadata $metadata */
        $metadata = $event->getParam('metadata');
        $mapping  = $this->getAssociationMapping($event);
        if (!$mapping || !$metadata->isCollectionValuedAssociation($event->getParam('name'))) {
            return;
        }

        $this->prepareEvent($event);

        /** @var \ArrayObject $elementSpec */
        $elementSpec = $event->getParam('elementSpec');
        $inputSpec   = $event->getParam('inputSpec');
        $inputSpec['required'] = false;

        $this->mergeAssociationOptions($elementSpec, $mapping['targetEntity']);

        $elementSpec['spec']['attributes']['multiple'] = true;
    }

    /**
     * @param EventInterface $event
     * @internal
     * @return bool
     */
    public function handleExcludeAssociation(EventInterface $event)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $metadata */
        $metadata = $event->getParam('metadata');
        return $metadata && $metadata->isAssociationInverseSide($event->getParam('name'));
    }

    /**
     * @param EventInterface $event
     * @internal
     * @return bool
     */
    public function handleExcludeField(EventInterface $event)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $metadata */
        $metadata    = $event->getParam('metadata');
        $identifiers = $metadata->getIdentifierFieldNames();

        return in_array($event->getParam('name'), $identifiers) &&
               $metadata->generatorType === ClassMetadata::GENERATOR_TYPE_IDENTITY;
    }

    /**
     * @param EventInterface $event
     * @internal
     */
    public function handleFilterField(EventInterface $event)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadata $metadata */
        $metadata = $event->getParam('metadata');
        if (!$metadata || !$metadata->hasField($event->getParam('name'))) {
            return;
        }

        $this->prepareEvent($event);

        $inputSpec = $event->getParam('inputSpec');

        switch ($metadata->getTypeOfField($event->getParam('name'))) {
            case 'bool':
            case 'boolean':
                $inputSpec['filters'][] = array('name' => 'Boolean');
                break;
            case 'bigint':
            case 'integer':
            case 'smallint':
                $inputSpec['filters'][] = array('name' => 'Int');
                break;
            case 'datetime':
            case 'datetimetz':
            case 'date':
            case 'time':
            case 'string':
            case 'text':
                $inputSpec['filters'][] = array('name' => 'StringTrim');
                break;
        }
    }

    /**
     * @param EventInterface $event
     * @internal
     */
    public function handleRequiredAssociation(EventInterface $event)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadata $metadata */
        $metadata = $event->getParam('metadata');
        $mapping  = $this->getAssociationMapping($event);
        if (!$mapping) {
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
                if (isset($joinColumn['nullable']) && $joinColumn['nullable']) {
                    $required = false;

                    if (!isset($elementSpec['spec']['options']['empty_option'])) {
                        $elementSpec['spec']['options']['empty_option'] = 'NULL';
                    }
                    break;
                }
            }

            $inputSpec['required'] = $required;
        }
    }

    /**
     * @param EventInterface $event
     * @internal
     */
    public function handleRequiredField(EventInterface $event)
    {
        $mapping = $this->getFieldMapping($event);
        if (!$mapping) {
            return;
        }

        $this->prepareEvent($event);

        $inputSpec             = $event->getParam('inputSpec');
        $inputSpec['required'] = isset($mapping['nullable']) ? !$mapping['nullable'] : true;
    }

    /**
     * @param EventInterface $event
     * @internal
     */
    public function handleTypeField(EventInterface $event)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadata $metadata */
        $metadata = $event->getParam('metadata');
        $mapping  = $this->getFieldMapping($event);
        if (!$mapping) {
            return;
        }

        $this->prepareEvent($event);

        $elementSpec = $event->getParam('elementSpec');

        switch ($metadata->getTypeOfField($event->getParam('name'))) {
            case 'bigint':
            case 'integer':
            case 'smallint':
                $type = 'Zend\Form\Element\Number';
                break;
            case 'bool':
            case 'boolean':
                $type = 'Zend\Form\Element\Checkbox';
                break;
            case 'date':
                $type = 'Zend\Form\Element\DateSelect';
                break;
            case 'datetimetz':
            case 'datetime':
                $type = 'Zend\Form\Element\DateTimeSelect';
                break;
            case 'time':
                $type = 'Zend\Form\Element\Time';
                break;
            case 'text':
                $type = 'Zend\Form\Element\Text';
                break;
            default:
                $type = 'Zend\Form\Element';
                break;
        }

        $elementSpec['spec']['type'] = $type;
    }

    /**
     * @param EventInterface $event
     * @internal
     */
    public function handleValidatorField(EventInterface $event)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadata $metadata */
        $mapping  = $this->getFieldMapping($event);
        $metadata = $event->getParam('metadata');
        if (!$mapping) {
            return;
        }

        $this->prepareEvent($event);

        $inputSpec = $event->getParam('inputSpec');

        switch ($metadata->getTypeOfField($event->getParam('name'))) {
            case 'bool':
            case 'boolean':
                $inputSpec['validators'][] = array(
                    'name'    => 'InArray',
                    'options' => array('haystack' => array('0', '1'))
                );
                break;
            case 'float':
                $inputSpec['validators'][] = array('name' => 'Float');
                break;
            case 'bigint':
            case 'integer':
            case 'smallint':
                $inputSpec['validators'][] = array('name' => 'Int');
                break;
            case 'string':
                if (isset($mapping['length'])) {
                    $inputSpec['validators'][] = array(
                        'name'    => 'StringLength',
                        'options' => array('max' => $mapping['length'])
                    );
                }
                break;
        }
    }

    /**
     * @param EventInterface $event
     * @return array|null
     */
    protected function getFieldMapping(EventInterface $event)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $metadata */
        $metadata = $event->getParam('metadata');
        if ($metadata && $metadata->hasField($event->getParam('name'))) {
            return $metadata->getFieldMapping($event->getParam('name'));
        }
        return null;
    }

    /**
     * @param EventInterface $event
     * @return array|null
     */
    protected function getAssociationMapping(EventInterface $event)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $metadata */
        $metadata = $event->getParam('metadata');
        if ($metadata && $metadata->hasAssociation($event->getParam('name'))) {
            return $metadata->getAssociationMapping($event->getParam('name'));
        }
        return null;
    }

    /**
     * @param ArrayObject $elementSpec
     * @param string $targetEntity
     */
    protected function mergeAssociationOptions(ArrayObject $elementSpec, $targetEntity)
    {
        $options = isset($elementSpec['spec']['options']) ? $elementSpec['spec']['options'] : array();
        $options = array_merge(
            array(
                'object_manager' => $this->objectManager,
                'target_class'   => $targetEntity
            ),
            $options
        );

        $elementSpec['spec']['options'] = $options;
        $elementSpec['spec']['type']    = 'DoctrineORMModule\Form\Element\EntitySelect';
    }

    /**
     * Normalizes event setting all expected parameters.
     *
     * @param EventInterface $event
     */
    protected function prepareEvent(EventInterface $event)
    {
        foreach (array('elementSpec', 'inputSpec') as $type) {
            if (!$event->getParam($type)) {
                $event->setParam($type, new ArrayObject());
            }
        }

        $elementSpec = $event->getParam('elementSpec');
        $inputSpec   = $event->getParam('inputSpec');

        if (!isset($elementSpec['spec'])) {
            $elementSpec['spec'] = array();
        }
        if (!isset($inputSpec['filters'])) {
            $inputSpec['filters'] = array();
        }
        if (!isset($inputSpec['validators'])) {
            $inputSpec['validators'] = array();
        }
    }
}
