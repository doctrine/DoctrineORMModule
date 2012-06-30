<?php

namespace DoctrineORMModule\Form\Annotation;

use Doctrine\ORM\EntityManager;
use Zend\Code\Annotation\AnnotationManager;
use Zend\EventManager\EventManagerInterface;
use Zend\Form\Annotation\AnnotationBuilder as ZendAnnotationBuilder;

class AnnotationBuilder extends ZendAnnotationBuilder
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * Constructor. Ensures EntityManager is present.
     *
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Override default setAnnotationManager to add custom annotations for Doctrine.
     *
     * @param \Zend\Code\Annotation\AnnotationManager $annotationManager
     * @return void|\Zend\Form\Annotation\AnnotationBuilder
     */
    public function setAnnotationManager(AnnotationManager $annotationManager)
    {
        parent::setAnnotationManager($annotationManager);

        $annotationManager->registerAnnotation(new Column);
        $annotationManager->registerAnnotation(new GeneratedValue);

        $annotationManager->setAlias('Doctrine\ORM\Mapping\Column', 'DoctrineORMModule\Form\Annotation\Column');
        $annotationManager->setAlias('Doctrine\ORM\Mapping\GeneratedValue', 'DoctrineORMModule\Form\Annotation\GeneratedValue');

        return $this;
    }

    /**
     * Set event manager instance
     *
     * @param  EventManagerInterface $events
     * @return self
     */
    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);

        $this->getEventManager()->attach(new ElementAnnotationsListener);

        return $this;
    }
}