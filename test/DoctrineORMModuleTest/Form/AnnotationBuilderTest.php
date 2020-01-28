<?php

namespace DoctrineORMModuleTest\Form;

use DoctrineORMModule\Form\Annotation\AnnotationBuilder;
use DoctrineORMModuleTest\Assets\Entity\FormEntity;
use DoctrineORMModuleTest\Assets\Entity\Issue237;
use DoctrineORMModuleTest\Framework\TestCase;
use Laminas\Form\Annotation\AnnotationBuilder as LaminasAnnotationBuilder;

class AnnotationBuilderTest extends TestCase
{
    /**
     * @var AnnotationBuilder
     */
    protected $builder;

    public function setUp() : void
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
        $spec = $this->builder->getFormSpecification(\DoctrineORMModuleTest\Assets\Entity\Issue237::class);

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
            if (isset($elementSpec['spec']['options'])) {
                if (array_key_exists('empty_option', $elementSpec['spec']['options']) &&
                    $elementSpec['spec']['options']['empty_option'] === null
                ) {
                    $showEmptyValue = false;
                    break;
                }
            }
        }
        $this->assertFalse($showEmptyValue);
    }

    /**
     * Ensure user defined \Type or type attribute overrides the listener one
     */
    public function testEnsureCustomTypeOrAttributeTypeIsUsedInAnnotations()
    {
        $userDefinedTypeOverridesListenerType = true;
        $entity                               = new FormEntity();

        $laminasAnnotationBuilder = new LaminasAnnotationBuilder();
        $laminasForm                 = $laminasAnnotationBuilder->createForm($entity);

        $spec           = $this->builder->getFormSpecification($entity);
        $annotationForm = $this->builder->createForm($entity);

        $attributesToTest = ['specificType', 'specificMultiType', 'specificAttributeType'];

        foreach ($spec['elements'] as $element) {
            $elementName = $element['spec']['name'];
            if (in_array($elementName, $attributesToTest)) {
                $annotationFormElement = $annotationForm->get($elementName);
                $laminasFormElement       = $laminasForm->get($elementName);

                $annotationElementAttribute = $annotationFormElement->getAttribute('type');
                $laminasElementAttribute       = $laminasFormElement->getAttribute('type');

                if ((get_class($laminasFormElement) !== get_class($annotationFormElement)) ||
                    ($annotationElementAttribute !== $laminasElementAttribute)
                ) {
                    $userDefinedTypeOverridesListenerType = false;
                }
            }
        }
        $this->assertTrue($userDefinedTypeOverridesListenerType);
    }

    /**
     * @link https://github.com/zendframework/zf2/issues/7096
     */
    public function testFileTypeDoesntGrabStringLengthValidator()
    {
        $validators = $this
            ->builder
            ->createForm(new FormEntity())
            ->getInputFilter()
            ->get('image')
            ->getValidatorChain()
            ->getValidators();

        $this->assertCount(0, $validators);
    }

    /**
     * Ensure prefer_form_input_filter is set to true for the generated form
     */
    public function testPreferFormInputFilterIsTrue()
    {
        $entity = new FormEntity();
        $form   = $this->builder->createForm($entity);
        $this->assertTrue($form->getPreferFormInputFilter());
    }
}
