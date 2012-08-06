<?php

namespace DoctrineORMModule\Form\Annotation;

use Doctrine\ORM\EntityManager;
use Zend\Code\Annotation\AnnotationManager;
use Zend\Code\Annotation\Parser;
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
     * Set annotation manager to use when building form from annotations
     *
     * @param  AnnotationManager $annotationManager
     * @return AnnotationBuilder
     */
    public function setAnnotationManager(AnnotationManager $annotationManager)
    {
        parent::setAnnotationManager($annotationManager);

        $parser = new Parser\DoctrineAnnotationParser();
        $parser->registerAnnotation('Doctrine\ORM\Mapping\Column');
        $parser->registerAnnotation('Doctrine\ORM\Mapping\GeneratedValue');

        $this->annotationManager->attach($parser);

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
