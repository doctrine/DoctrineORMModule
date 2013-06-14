<?php

namespace DoctrineORMModuleTest\Form;

use DoctrineORMModule\Form\Annotation\AnnotationBuilder;
use DoctrineORMModuleTest\Assets\Entity\Issue237;
use DoctrineORMModuleTest\Framework\TestCase;

class AnnotationBuilderTest extends TestCase
{
    /**
     * @var AnnotationBuilder
     */
    protected $builder;

    public function setUp()
    {
        $this->builder = new AnnotationBuilder($this->getEntityManager());
    }

    /**
     * @cover DoctrineORMModuleTest\Form\AnnotationBuilder::getFormSpecification
     * @link https://github.com/doctrine/DoctrineORMModule/issues/237
     */
    public function testIssue237()
    {
        $entity = new Issue237();
        $spec   = $this->builder->getFormSpecification($entity);
        $this->assertCount(0, $spec['elements']);
    }
}
