<?php
namespace DoctrineORMModule\Form\Annotation;

use ArrayObject;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use DoctrineModule\Form\Element;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

class ElementAnnotationsListener implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

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
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if (false !== $events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('configureElementField', array($this, 'handleFilterField'));

        $this->listeners[] = $events->attach('configureElementField', array($this, 'handleTypeField'));

        $this->listeners[] = $events->attach('configureElementField', array($this, 'handleValidatorField'));

        $this->listeners[] = $events->attach('configureElementField', array($this, 'handleRequiredField'));
        $this->listeners[] = $events->attach('configureElementAssociation', array($this, 'handleRequiredAssociation'));

        $this->listeners[] = $events->attach('checkForExcludeField', array($this, 'handleExcludeField'));
        $this->listeners[] = $events->attach('checkForExcludeAssociation', array($this, 'handleExcludeAssociation'));

        $this->listeners[] = $events->attach('configureElementAssociation', array($this, 'handleToOne'));
        $this->listeners[] = $events->attach('configureElementAssociation', array($this, 'handleToMany'));
    }


    public function handleToOne(EventInterface $event)
    {
        $metadata = $this->getAssociationMetadata($event);
        if (!$metadata || !($metadata['type'] & ClassMetadataInfo::TO_ONE)) {
            return;
        }

        $elementSpec = $event->getParam('elementSpec');
        $inputSpec   = $event->getParam('inputSpec');

        if (!isset($elementSpec['spec'])) {
            $elementSpec['spec'] = array();
        }

        $this->mergeElementOptions($elementSpec, $metadata['targetEntity']);

        foreach ($metadata['joinColumns'] as $joinColumn) {
            if (isset($joinColumn['nullable']) && $joinColumn['nullable']) {
                $inputSpec['required'] = false;

                if (!isset($elementSpec['spec']['options']['empty_option'])) {
                    $elementSpec['spec']['options']['empty_option'] = 'NULL';
                }
                break;
            }
        }

        $elementSpec['spec']['type'] = 'DoctrineORMModule\Form\Element\EntitySelect';
    }

    public function handleToMany(EventInterface $event)
    {
        $metadata = $this->getAssociationMetadata($event);
        if (!$metadata || !($metadata['type'] & ClassMetadataInfo::TO_MANY)) {
            return;
        }

        /** @var \ArrayObject $elementSpec */
        $elementSpec = $event->getParam('elementSpec');
        $inputSpec   = $event->getParam('inputSpec');
        $inputSpec['required'] = false;

        if (!isset($elementSpec['spec'])) {
            $elementSpec['spec'] = array();
        }

        $this->mergeElementOptions($elementSpec, $metadata['targetEntity']);

        $elementSpec['spec']['type']                   = 'DoctrineORMModule\Form\Element\EntitySelect';
        $elementSpec['spec']['attributes']['multiple'] = true;
    }

    public function handleExcludeAssociation(EventInterface $event)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $metadata */
        $metadata = $event->getParam('metadata');
        if ($metadata->isAssociationInverseSide($event->getParam('name'))) {
            return true;
        }
        return false;
    }

    public function handleExcludeField(EventInterface $event)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $metadata */
        $metadata    = $event->getParam('metadata');
        $identifiers = $metadata->getIdentifierFieldNames();

        if (!in_array($event->getParam('name'), $identifiers)) {
            return false;
        }

        if ($metadata->generatorType === ClassMetadata::GENERATOR_TYPE_IDENTITY) {
            return true;
        }

        return false;
    }

    public function handleFilterField(EventInterface $event)
    {
        $metadata  = $this->getFieldMetdata($event);
        $inputSpec = $event->getParam('inputSpec');

        if (!isset($inputSpec['filters'])) {
            $inputSpec['filters'] = array();
        }

        switch ($metadata['type']) {
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

    public function handleRequiredAssociation(EventInterface $event)
    {
        $metadata  = $this->getAssociationMetadata($event);
        $inputSpec = $event->getParam('inputSpec');

        if ($metadata['type'] & ClassMetadataInfo::TO_MANY) {
            $inputSpec['required'] = false;
        } else if (isset($metadata['joinColumns'])) {
            foreach ($metadata['joinColumns'] as $joinColumn) {
                if (isset($joinColumn['nullable']) && $joinColumn['nullable']) {
                    $inputSpec['required'] = false;
                    break;
                }
            }
        }
    }

    public function handleRequiredField(EventInterface $event)
    {
        $metadata  = $this->getFieldMetdata($event);
        $inputSpec = $event->getParam('inputSpec');

        if (!isset($metadata['nullable'])) {
            return;
        }
        $inputSpec['required'] = (bool) !$metadata['nullable'];
    }

    public function handleTypeField(EventInterface $event)
    {
        $metadata    = $this->getFieldMetdata($event);
        $elementSpec = $event->getParam('elementSpec');

        switch ($metadata['type']) {
            case 'bool':
            case 'boolean':
                $type = 'Zend\Form\Element\Checkbox';
                break;
            case 'date':
                $type = 'Zend\Form\Element\Date';
                break;
            case 'datetime':
                $type = 'Zend\Form\Element\DateTime';
                break;
            default:
                $type = 'Zend\Form\Element';
                break;
        }

        $elementSpec['spec']['type'] = $type;
    }

    public function handleValidatorField(EventInterface $event)
    {
        $metadata  = $this->getFieldMetdata($event);
        $inputSpec = $event->getParam('inputSpec');

        if (!isset($inputSpec['validators'])) {
            $inputSpec['validators'] = array();
        }

        switch ($metadata['type']) {
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
                if (isset($metadata['length'])) {
                    $inputSpec['validators'][] = array(
                        'name'    => 'StringLength',
                        'options' => array('max' => $metadata['length'])
                    );
                }
                break;
        }
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }

    /**
     * @param EventInterface $event
     * @return array|null
     */
    protected function getFieldMetdata(EventInterface $event)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $metadata */
        $metadata = $event->getParam('metadata');
        if (!$metadata) {
            return null;
        }
        return $metadata->getFieldMapping($event->getParam('name'));
    }

    /**
     * @param EventInterface $event
     * @return array|null
     */
    protected function getAssociationMetadata(EventInterface $event)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $metadata */
        $metadata = $event->getParam('metadata');
        if (!$metadata) {
            return null;
        }
        return $metadata->getAssociationMapping($event->getParam('name'));
    }

    /**
     * @param ArrayObject $elementSpec
     * @param string $targetEntity
     */
    protected function mergeElementOptions(ArrayObject $elementSpec, $targetEntity)
    {
        $options = isset($elementSpec['spec']['options']) ? $elementSpec['spec']['options'] : array();
        $options = array_merge(
            array(
                'object_manager' => $this->getObjectManager(),
                'target_class'   => $targetEntity
            ),
            $options
        );
        $elementSpec['spec']['options'] = $options;
    }
}
