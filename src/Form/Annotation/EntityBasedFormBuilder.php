<?php

declare(strict_types=1);

namespace DoctrineORMModule\Form\Annotation;

use ArrayObject;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectManager;
use DoctrineModule\Form\Element\ObjectMultiCheckbox;
use DoctrineModule\Form\Element\ObjectRadio;
use DoctrineModule\Form\Element\ObjectSelect;
use DoctrineORMModule\Form\Element\EntityMultiCheckbox;
use DoctrineORMModule\Form\Element\EntityRadio;
use DoctrineORMModule\Form\Element\EntitySelect;
use Laminas\Form\Annotation\AbstractBuilder;
use Laminas\Form\Annotation\AnnotationBuilder as LaminasAnnotationBuilder;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Form\FormInterface;
use Laminas\Stdlib\ArrayUtils;
use RuntimeException;

use function class_exists;
use function in_array;
use function is_object;
use function sprintf;

final class EntityBasedFormBuilder
{
    public const EVENT_CONFIGURE_FIELD       = 'configureField';
    public const EVENT_CONFIGURE_ASSOCIATION = 'configureAssociation';
    public const EVENT_EXCLUDE_FIELD         = 'excludeField';
    public const EVENT_EXCLUDE_ASSOCIATION   = 'excludeAssociation';

    protected AbstractBuilder $builder;

    protected ObjectManager $objectManager;

    /**
     * Constructor. Ensures ObjectManager is present.
     */
    public function __construct(ObjectManager $objectManager, ?AbstractBuilder $builder = null)
    {
        if (! class_exists(AbstractBuilder::class)) {
            throw new RuntimeException(sprintf(
                'Usage of %s requires laminas-form 3.0.0 or newer, which currently is not installed.',
                self::class
            ));
        }

        $this->objectManager = $objectManager;
        $this->builder       = $builder ?? new LaminasAnnotationBuilder();
        (new DoctrineAnnotationListener($this->objectManager))->attach($this->builder->getEventManager());
    }

    /**
     * @return AbstractBuilder the form builder from laminas-form
     */
    public function getBuilder(): AbstractBuilder
    {
        return $this->builder;
    }

    /**
     * Overrides the base getFormSpecification() to additionally iterate through each
     * field/association in the metadata and trigger the associated event.
     *
     * This allows building of a form from metadata instead of requiring annotations.
     * Annotations are still allowed through the ElementAnnotationsListener.
     *
     * @param  class-string|object $entity Either an instance or a valid class name for an entity
     *
     * @throws InvalidArgumentException    If $entity is not an object or class name.
     */
    public function getFormSpecification(string|object $entity): ArrayObject
    {
        $formSpec    = $this->getBuilder()->getFormSpecification($entity);
        $metadata    = $this->objectManager->getClassMetadata(is_object($entity) ? $entity::class : $entity);
        $inputFilter = $formSpec['input_filter'];

        $formElements = [
            EntitySelect::class,
            EntityMultiCheckbox::class,
            EntityRadio::class,
            ObjectSelect::class,
            ObjectMultiCheckbox::class,
            ObjectRadio::class,
        ];

        foreach ($formSpec['elements'] as $key => $elementSpec) {
            $name          = $elementSpec['spec']['name'] ?? null;
            $isFormElement = (isset($elementSpec['spec']['type']) &&
                in_array($elementSpec['spec']['type'], $formElements));

            if (! $name) {
                continue;
            }

            if (! isset($inputFilter[$name])) {
                $inputFilter[$name] = new ArrayObject();
            }

            $params = [
                'metadata'    => $metadata,
                'name'        => $name,
                'elementSpec' => $elementSpec,
                'inputSpec'   => $inputFilter[$name],
            ];

            if ($this->checkForExcludeElementFromMetadata($metadata, $name)) {
                $elementSpec = $formSpec['elements'];
                unset($elementSpec[$key]);
                $formSpec['elements'] = $elementSpec;

                if (isset($inputFilter[$name])) {
                    unset($inputFilter[$name]);
                }

                $formSpec['input_filter'] = $inputFilter;
                continue;
            }

            if ($metadata->hasField($name) || (! $metadata->hasAssociation($name) && $isFormElement)) {
                $this->getBuilder()->getEventManager()->trigger(self::EVENT_CONFIGURE_FIELD, $this, $params);
            } elseif ($metadata->hasAssociation($name)) {
                $this->getBuilder()->getEventManager()->trigger(self::EVENT_CONFIGURE_ASSOCIATION, $this, $params);
            }
        }

        $formSpec['options'] = ['prefer_form_input_filter' => true];

        return $formSpec;
    }

    /**
     * Create a form from an object.
     *
     * @param class-string|object $entity
     */
    public function createForm(string|object $entity): FormInterface
    {
        $formSpec    = ArrayUtils::iteratorToArray($this->getFormSpecification($entity));
        $formFactory = $this->getBuilder()->getFormFactory();

        return $formFactory->createForm($formSpec);
    }

    private function checkForExcludeElementFromMetadata(ClassMetadata $metadata, string $name): bool
    {
        $params = ['metadata' => $metadata, 'name' => $name];
        $result = false;

        if ($metadata->hasField($name)) {
            $result = $this->getBuilder()->getEventManager()->trigger(self::EVENT_EXCLUDE_FIELD, $this, $params);
        } elseif ($metadata->hasAssociation($name)) {
            $result = $this->getBuilder()->getEventManager()->trigger(self::EVENT_EXCLUDE_ASSOCIATION, $this, $params);
        }

        if ($result) {
            $result = (bool) $result->last();
        }

        return $result;
    }
}
