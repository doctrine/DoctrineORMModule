<?php

declare(strict_types=1);

namespace DoctrineORMModuleTest\Form\Annotation;

use DoctrineORMModule\Form\Annotation\EntityBasedFormBuilder;
use DoctrineORMModuleTest\Assets\Entity\FormEntity;
use DoctrineORMModuleTest\Assets\Entity\Issue237;
use DoctrineORMModuleTest\Framework\TestCase;
use Laminas\Form\Annotation\AbstractBuilder;
use Laminas\Form\Annotation\AnnotationBuilder as LaminasAnnotationBuilder;
use Laminas\Form\Form;

use function array_key_exists;
use function class_exists;
use function get_class;
use function in_array;
use function sprintf;

class EntityBasedFormBuilderTest extends TestCase
{
    protected EntityBasedFormBuilder $builder;

    public function setUp(): void
    {
        if (! class_exists(AbstractBuilder::class)) {
            $this->markTestSkipped(sprintf(
                '%s requires laminas-form 3.0.0 or newer to be installed.',
                EntityBasedFormBuilder::class
            ));
        }

        $this->builder = new EntityBasedFormBuilder($this->getEntityManager());
    }

    /**
     * @link https://github.com/doctrine/DoctrineORMModule/issues/237
     */
    public function testIssue237(): void
    {
        $entity = new Issue237();
        $spec   = $this->builder->getFormSpecification($entity);

        $this->assertCount(0, $spec['elements']);
    }

    public function testAnnotationBuilderSupportsClassNames(): void
    {
        $spec = $this->builder->getFormSpecification(Issue237::class);

        $this->assertCount(0, $spec['elements'], 'Annotation builder allows also class names');
    }

    /**
     * empty_option behavior - !isset can't evaluate null value
     *
     * @link https://github.com/doctrine/DoctrineORMModule/pull/247
     */
    public function testEmptyOptionNullDoesntGenerateValue(): void
    {
        $showEmptyValue = true;
        $entity         = new FormEntity();
        $spec           = $this->builder->getFormSpecification($entity);

        foreach ($spec['elements'] as $elementSpec) {
            if (! isset($elementSpec['spec']['options'])) {
                continue;
            }

            if (
                array_key_exists('empty_option', $elementSpec['spec']['options']) &&
                $elementSpec['spec']['options']['empty_option'] === null
            ) {
                $showEmptyValue = false;
                break;
            }
        }

        $this->assertFalse($showEmptyValue);
    }

    /**
     * Ensure user defined \Type or type attribute overrides the listener one
     */
    public function testEnsureCustomTypeOrAttributeTypeIsUsedInAnnotations(): void
    {
        $userDefinedTypeOverridesListenerType = true;
        $entity                               = new FormEntity();

        $laminasAnnotationBuilder = new LaminasAnnotationBuilder();
        $laminasForm              = $laminasAnnotationBuilder->createForm($entity);

        $spec           = $this->builder->getFormSpecification($entity);
        $annotationForm = $this->builder->createForm($entity);

        $attributesToTest = ['specificType', 'specificMultiType', 'specificAttributeType'];

        foreach ($spec['elements'] as $element) {
            $elementName = $element['spec']['name'];
            if (! in_array($elementName, $attributesToTest)) {
                continue;
            }

            $annotationFormElement = $annotationForm->get($elementName);
            $laminasFormElement    = $laminasForm->get($elementName);

            $annotationElementAttribute = $annotationFormElement->getAttribute('type');
            $laminasElementAttribute    = $laminasFormElement->getAttribute('type');

            if (
                (get_class($laminasFormElement) === get_class($annotationFormElement)) &&
                ($annotationElementAttribute === $laminasElementAttribute)
            ) {
                continue;
            }

            $userDefinedTypeOverridesListenerType = false;
        }

        $this->assertTrue($userDefinedTypeOverridesListenerType);
    }

    /**
     * @link https://github.com/zendframework/zf2/issues/7096
     */
    public function testFileTypeDoesntGrabStringLengthValidator(): void
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
    public function testPreferFormInputFilterIsTrue(): void
    {
        $entity = new FormEntity();
        $form   = $this->builder->createForm($entity);

        $this->assertInstanceOf(Form::class, $form);
        $this->assertTrue($form->getPreferFormInputFilter());
    }
}
