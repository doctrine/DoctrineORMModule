<?php

namespace DoctrineORMModule\Service;

use DoctrineModule\Service\AbstractFactory;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder;
use Interop\Container\ContainerInterface;
use Zend\Form\Factory;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Service factory responsible for instantiating {@see \DoctrineORMModule\Form\Annotation\AnnotationBuilder}
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Marco Pivetta <ocramius@gmail.com>
 */
class FormAnnotationBuilderFactory extends AbstractFactory
{
    /**
     * {@inheritDoc}
     *
     * @return \DoctrineORMModule\Form\Annotation\AnnotationBuilder
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var $entityManager \Doctrine\ORM\EntityManager */
        $entityManager = $container->get('doctrine.entitymanager.' . $this->getName());

        $annotationBuilder = new AnnotationBuilder($entityManager);

        $annotationBuilder->setFormFactory($this->getFormFactory($container));

        return $annotationBuilder;
    }

    /**
     * {@inheritDoc}
     *
     * @return \DoctrineORMModule\Form\Annotation\AnnotationBuilder
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, \DoctrineORMModule\Form\Annotation\AnnotationBuilder::class);
    }

    /**
     * {@inheritDoc}
     */
    public function getOptionsClass()
    {
    }

    /**
     * Retrieve the form factory
     *
     * @param  ContainerInterface $services
     * @return Factory
     */
    private function getFormFactory(ContainerInterface $services)
    {
        $elements = null;

        if ($services->has('FormElementManager')) {
            $elements = $services->get('FormElementManager');
        }

        return new Factory($elements);
    }
}
