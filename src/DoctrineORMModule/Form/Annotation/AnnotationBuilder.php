<?php

namespace DoctrineORMModule\Form\Annotation;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use Zend\Code\Annotation\AnnotationManager;
use Zend\EventManager\EventManagerInterface;
use Zend\Form\Annotation\AnnotationBuilder as ZendAnnotationBuilder;

class AnnotationBuilder extends ZendAnnotationBuilder
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $objectManager;

    /**
     * Constructor. Ensures ObjectManager is present.
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }

    /**
     * {@inheritDoc}
     */
    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);

        $this->getEventManager()->attach(new ElementAnnotationsListener($this->getObjectManager()));

        return $this;
    }

    /**
     * Overrides the base getFormSpecification() to additionally iterate through each
     * field/association in the metadata and trigger the associated event.
     *
     * This allows building of a form from metadata instead of requiring annotations.
     * Annotations are still allowed through the ElementAnnotationsListener.
     *
     * {@inheritDoc}
     */
    public function getFormSpecification($entity)
    {
        $formSpec = parent::getFormSpecification($entity);
        $metadata = $this->getObjectManager()->getClassMetadata(get_class($entity));

        $inputSpec = $formSpec['input_filter'];
        foreach ($formSpec['elements'] as $key => $elementSpec) {
            $name = isset($elementSpec['spec']['name']) ? $elementSpec['spec']['name'] : null;

            if (!$name) {
                continue;
            }

            $params = array(
                'metadata'    => $metadata,
                'name'        => $name,
                'elementSpec' => $elementSpec,
                'inputSpec'   => isset($inputSpec[$name]) ? $inputSpec[$name] : new \ArrayObject()
            );

            if ($this->excludeElementFromMetadata($metadata, $name)) {
                unset($formSpec['elements'][$key]);
                unset($inputSpec[$name]);
                continue;
            }

            if ($metadata->hasField($name)) {
                $this->getEventManager()->trigger('configureElementField', $this, $params);
            } else if ($metadata->hasAssociation($name)) {
                $this->getEventManager()->trigger('configureElementAssociation', $this, $params);
            }
        }

        return $formSpec;
    }

    protected function excludeElementFromMetadata(ClassMetadata $metadata, $name)
    {
        $params = array('metadata' => $metadata, 'name' => $name);
        $test   = function($r) { return (true === $r); };
        $result = false;

        if ($metadata->hasField($name)) {
            $result = $this->getEventManager()->trigger('checkForExcludeField', $this, $params, $test);
        } else if ($metadata->hasAssociation($name)) {
            $result = $this->getEventManager()->trigger('checkForExcludeAssociation', $this, $params, $test);
        }

        if ($result) {
            $result = (bool) $result->last();
        }

        return $result;
    }
}
