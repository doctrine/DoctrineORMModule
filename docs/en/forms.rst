Laminas Forms
=============

DoctrineModule and DoctrineORMModule provide an integration with `laminas-form <https://docs.laminas.dev/laminas-form/>`_.

Creating Forms using Entity Annotations
---------------------------------------

With laminas-form, forms can be created using `PHP8 attributes or DocBlock annotations <https://docs.laminas.dev/laminas-form/v3/form-creation/attributes-or-annotations/>`_.
DoctrineORMModule extends this feature to support Doctrine-specific form elements (see next section).

First, create a form builder instance. By default, this uses the ``AnnotationBuilder`` from laminas-form,
which uses DocBlock annotations. Alternatively, you can provide an ``AttributeBuilder`` to use PHP8-style
attributes.

.. code:: php

    // using PhpDoc annotations
    $entityManager = $container->get(\Doctrine\ORM\EntityManager::class);
    $builder = new \DoctrineORMModule\Form\Annotation\EntityBasedFormBuilder($entityManager);

    // alternatively, to use PHP8 attributes
    $entityManager = $container->get(\Doctrine\ORM\EntityManager::class);
    $attributeBuilder = new \Laminas\Form\Annotation\AttributeBuilder();
    $builder = new \DoctrineORMModule\Form\Annotation\EntityBasedFormBuilder($entityManager, $attributeBuilder);

Given an entity instance, the form builder can either create a form specification or directly a form instance:

.. code:: php

    $entity = new User();

    // get form specification only
    $formSpec = $builder->getFormSpecification($entity);

    // or directly get form
    $form= $builder->createForm($entity);

Extension points for customizing the form builder are the event manager and the form factory, which can
be accessed as follows:

.. code:: php

    // if you need access to the event manager
    $myListener = new MyListener();
    $myListener->attach($builder->getBuilder()->getEventManager());

    // if you need access to the form factory
    $formElementManager = $container->get(\Laminas\Form\FormElementManager::class)
    $builder->getBuilder()->getFormFactory()->setFormElementManager($formElementManager);

Doctrine-specific Form Elements
-------------------------------

DoctrineModule provides three Doctrine-specific form elements:

- ``DoctrineModule\Form\Element\ObjectSelect``
- ``DoctrineModule\Form\Element\ObjectRadio``
- ``DoctrineModule\Form\Element\ObjectMultiCheckbox``

Please read the `DoctrineModule documentation on form elements <https://www.doctrine-project.org/projects/doctrine-module/en/current/form-element.html>`_
for further information.

Doctrine-specific Validators
----------------------------

DoctrineModule provides three Doctrine-specific validators:

- ``DoctrineModule\Validator\ObjectExists``
- ``DoctrineModule\Validator\NoObjectExists``
- ``DoctrineModule\Validator\UniqueObject``

Please read the `DoctrineModule documentation on validators <https://www.doctrine-project.org/projects/doctrine-module/en/current/validator.html>`_
for further information.
