<?php

namespace DoctrineORMModule\Form\Annotation;

use Doctrine\ORM\EntityManager;
use Zend\Code\Annotation\AnnotationManager;
use Zend\Code\Annotation\Parser\DoctrineAnnotationParser;
use Zend\EventManager\EventManagerInterface;
use Zend\Form\Annotation\AnnotationBuilder as ZendAnnotationBuilder;

class AnnotationBuilder extends ZendAnnotationBuilder
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * Constructor. Ensures EntityManager is present.
     *
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
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

        $parser = new DoctrineAnnotationParser($this->entityManager);
        $parser->registerAnnotation('Doctrine\ORM\Mapping\Column');
        $parser->registerAnnotation('Doctrine\ORM\Mapping\GeneratedValue');
        $parser->registerAnnotation('Doctrine\ORM\Mapping\OneToMany');
        $parser->registerAnnotation('Doctrine\ORM\Mapping\OneToOne');
        $parser->registerAnnotation('Doctrine\ORM\Mapping\ManyToMany');
        $parser->registerAnnotation('Doctrine\ORM\Mapping\ManyToOne');

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

        $this->getEventManager()->attach(new ElementAnnotationsListener($this->entityManager));

        return $this;
    }
}
