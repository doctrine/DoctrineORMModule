<?php

declare(strict_types=1);

namespace DoctrineORMModule\Service;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Service\AbstractFactory;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder;
use Interop\Container\ContainerInterface;
use Laminas\Form\Factory;
use Laminas\ServiceManager\ServiceLocatorInterface;
use function assert;

/**
 * Service factory responsible for instantiating {@see \DoctrineORMModule\Form\Annotation\AnnotationBuilder}
 *
 * @link    http://www.doctrine-project.org/
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
        assert($entityManager instanceof EntityManager);

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

    /**
     * {@inheritDoc}
     */
    public function getOptionsClass()
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
