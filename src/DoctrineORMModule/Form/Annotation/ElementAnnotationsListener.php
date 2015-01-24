<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace DoctrineORMModule\Form\Annotation;

use ArrayObject;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use DoctrineModule\Form\Element;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;

/**
 * @author Kyle Spraggs <theman@spiffyjr.me>
 */
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
        $this->listeners[] = $events->attach(
            AnnotationBuilder::EVENT_CONFIGURE_FIELD,
            array($this, 'handleFilterField')
        );
        $this->listeners[] = $events->attach(
            AnnotationBuilder::EVENT_CONFIGURE_FIELD,
            array($this, 'handleTypeField')
        );
        $this->listeners[] = $events->attach(
            AnnotationBuilder::EVENT_CONFIGURE_FIELD,
            array($this, 'handleValidatorField')
        );
        $this->listeners[] = $events->attach(
            AnnotationBuilder::EVENT_CONFIGURE_FIELD,
            array($this, 'handleRequiredField')
        );
        $this->listeners[] = $events->attach(
            AnnotationBuilder::EVENT_EXCLUDE_FIELD,
            array($this, 'handleExcludeField')
        );
        $this->listeners[] = $events->attach(
            AnnotationBuilder::EVENT_CONFIGURE_ASSOCIATION,
            array($this, 'handleToOne')
        );
        $this->listeners[] = $events->attach(
            AnnotationBuilder::EVENT_CONFIGURE_ASSOCIATION,
            array($this, 'handleToMany')
        );
        $this->listeners[] = $events->attach(
            AnnotationBuilder::EVENT_CONFIGURE_ASSOCIATION,
            array($this, 'handleRequiredAssociation')
        );
        $this->listeners[] = $events->attach(
            AnnotationBuilder::EVENT_EXCLUDE_ASSOCIATION,
            array($this, 'handleExcludeAssociation')
        );
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
        $elementSpec           = $event->getParam('elementSpec');
        $inputSpec             = $event->getParam('inputSpec');
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
                    if ((isset($elementSpec['spec']['options']) &&
                         !array_key_exists('empty_option', $elementSpec['spec']['options'])) ||
                         !isset($elementSpec['spec']['options'])
                    ) {
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
        $this->prepareEvent($event);

        /** @var \Doctrine\ORM\Mapping\ClassMetadata $metadata */
        $metadata  = $event->getParam('metadata');
        $inputSpec = $event->getParam('inputSpec');

        if (!$metadata || !$metadata->hasField($event->getParam('name'))) {
            return;
        }

        $inputSpec['required'] = !$metadata->isNullable($event->getParam('name'));
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
                $type = 'Zend\Form\Element\Number';
                break;
            case 'bool':
            case 'boolean':
                $type = 'Zend\Form\Element\Checkbox';
                break;
            case 'date':
                $type = 'Zend\Form\Element\Date';
                break;
            case 'datetimetz':
            case 'datetime':
                $type = 'Zend\Form\Element\DateTime';
                break;
            case 'time':
                $type = 'Zend\Form\Element\Time';
                break;
            case 'text':
                $type = 'Zend\Form\Element\Textarea';
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
                $elementSpec = $event->getParam('elementSpec');
                if (isset($elementSpec['spec']['type']) &&
                    in_array($elementSpec['spec']['type'], array('File', 'Zend\Form\Element\File'))
                ) {
                    return;
                }

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
        if (!isset($elementSpec['spec']['type'])) {
            $elementSpec['spec']['type'] = 'DoctrineORMModule\Form\Element\EntitySelect';
        }
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
