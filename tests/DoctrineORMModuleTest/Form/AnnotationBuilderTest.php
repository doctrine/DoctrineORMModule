<?php

namespace DoctrineORMModuleTest\Form;

use DoctrineORMModule\Form\Annotation\AnnotationBuilder;
use DoctrineORMModuleTest\Assets\Entity\FormEntity;
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
     * @covers \DoctrineORMModule\Form\Annotation\AnnotationBuilder::getFormSpecification
     * @link   https://github.com/doctrine/DoctrineORMModule/issues/237
     */
    public function testIssue237()
    {
        $entity = new Issue237();
        $spec   = $this->builder->getFormSpecification($entity);

        $this->assertCount(0, $spec['elements']);
    }

    /**
     * @covers \DoctrineORMModule\Form\Annotation\AnnotationBuilder::getFormSpecification
     */
    public function testAnnotationBuilderSupportsClassNames()
    {
        $spec = $this->builder->getFormSpecification('DoctrineORMModuleTest\\Assets\\Entity\\Issue237');

        $this->assertCount(0, $spec['elements'], 'Annotation builder allows also class names');
    }

    /**
     * empty_option behavior - !isset can't evaluate null value
     * @link https://github.com/doctrine/DoctrineORMModule/pull/247
     */
    public function testEmptyOptionNullDoesntGenerateValue()
    {
        $showEmptyValue = true;
        $entity         = new FormEntity();
        $spec           = $this->builder->getFormSpecification($entity);

        foreach ($spec['elements'] as $elementSpec) {
            if (isset($elementSpec['spec']['options']) &&
                array_key_exists('empty_option', $elementSpec['spec']['options']) &&
                null === $elementSpec['spec']['options']['empty_option']
            ) {
                $showEmptyValue = false;
                break;
            }
        }

        $this->assertFalse($showEmptyValue);
    }
}
