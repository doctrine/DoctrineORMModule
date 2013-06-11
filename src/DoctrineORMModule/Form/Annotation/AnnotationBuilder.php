<?php

namespace DoctrineORMModule\Form\Annotation;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use Zend\Code\Annotation\AnnotationManager;
use Zend\EventManager\EventManagerInterface;
use Zend\Form\Annotation\AnnotationBuilder as ZendAnnotationBuilder;

class AnnotationBuilder extends ZendAnnotationBuilder
{
    const EVENT_CONFIGURE_FIELD       = 'configureField';
    const EVENT_CONFIGURE_ASSOCIATION = 'configureAssociation';
    const EVENT_EXCLUDE_FIELD         = 'excludeField';
    const EVENT_EXCLUDE_ASSOCIATION   = 'excludeAssociation';

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
     * {@inheritDoc}
     */
    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);

        $this->getEventManager()->attach(new ElementAnnotationsListener($this->objectManager));

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
        $metadata = $this->objectManager->getClassMetadata(get_class($entity));

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

            if ($this->checkForExcludeElementFromMetadata($metadata, $name)) {
                unset($formSpec['elements'][$key]);
                unset($inputSpec[$name]);
                continue;
            }

            if ($metadata->hasField($name)) {
                $this->getEventManager()->trigger(static::EVENT_CONFIGURE_FIELD, $this, $params);
            } elseif ($metadata->hasAssociation($name)) {
                $this->getEventManager()->trigger(static::EVENT_CONFIGURE_ASSOCIATION, $this, $params);
            }
        }

        return $formSpec;
    }

    /**
     * @param ClassMetadata $metadata
     * @param $name
     * @return bool
     */
    protected function checkForExcludeElementFromMetadata(ClassMetadata $metadata, $name)
    {
        $params = array('metadata' => $metadata, 'name' => $name);
        $test   = function($r) { return (true === $r); };
        $result = false;

        if ($metadata->hasField($name)) {
            $result = $this->getEventManager()->trigger(static::EVENT_EXCLUDE_FIELD, $this, $params, $test);
        } elseif ($metadata->hasAssociation($name)) {
            $result = $this->getEventManager()->trigger(static::EVENT_EXCLUDE_ASSOCIATION, $this, $params, $test);
        }

        if ($result) {
            $result = (bool) $result->last();
        }

        return $result;
    }
}
