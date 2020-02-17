<?php

declare(strict_types=1);

namespace DoctrineORMModule\Service;

use DoctrineModule\Service\AbstractFactory;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder;
use Interop\Container\ContainerInterface;
use Laminas\Form\Factory;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Service factory responsible for instantiating {@see \DoctrineORMModule\Form\Annotation\AnnotationBuilder}
 */
class FormAnnotationBuilderFactory extends AbstractFactory
{
    /**
     * {@inheritDoc}
     *
     * @return AnnotationBuilder
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.' . $this->getName());

        $annotationBuilder = new AnnotationBuilder($entityManager);
        $annotationBuilder->setFormFactory($this->getFormFactory($container));

        return $annotationBuilder;
    }

    /**
     * {@inheritDoc}
     *
     * @return AnnotationBuilder
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, AnnotationBuilder::class);
    }

    public function getOptionsClass() : string
    {
    }

    /**
     * Retrieve the form factory
     */
    private function getFormFactory(ContainerInterface $services) : Factory
    {
        $elements = null;

        if ($services->has('FormElementManager')) {
            $elements = $services->get('FormElementManager');
        }

        return new Factory($elements);
    }
}
