# 0.9.0
 * [`DoctrineORMModule\Form\Annotation\ElementAnnotationsListener`](https://github.com/doctrine/DoctrineORMModule/blob/master/src/DoctrineORMModule/Form/Annotation/ElementAnnotationsListener.php) and
   [`DoctrineORMModule\Form\Annotation\AnnotationBuilder`](https://github.com/doctrine/DoctrineORMModule/blob/master/src/DoctrineORMModule/Form/Annotation/AnnotationBuilder.php)
   were updated to properly handle 'empty_option'.
   According to ZF2 docs, setting empty_option to NULL instructs a \Zend\Form\Element\Select that it should not add an "empty option" to a select list.
   [#281](https://github.com/doctrine/DoctrineORMModule/pull/281)
 * Added possibility to define a specific form element in annotations. It now overrides the one defined by the listener when explicitly asked.
   It also reintroduces the DoctrineModule\Form\Element\ObjectSelect behavior of v0.7.0, that permitted to set an ObjectSelect on an entity's attribute when no association was defined (OneToMany etc.)
   [#272](https://github.com/doctrine/DoctrineORMModule/pull/272)
 * The required PHP version is bumped to `5.3.23` [#306](https://github.com/doctrine/DoctrineORMModule/pull/306)

# 0.8.0

 * [`DoctrineORMModule\Form\Annotation\AnnotationBuilder`](https://github.com/doctrine/DoctrineORMModule/blob/master/src/DoctrineORMModule/Form/Annotation/AnnotationBuilder.php)
   does now also handle associations [#193](https://github.com/doctrine/DoctrineORMModule/pull/193)
 * `DoctrineORMModule\Module` does not implement `Zend\ModuleManager\Feature\AutoloaderProviderInterface` anymore.
   Please switch to composer autoloading.
 * [`DoctrineORMModule\Form\Annotation\ElementAnnotationsListener`](https://github.com/doctrine/DoctrineORMModule/blob/master/src/DoctrineORMModule/Form/Annotation/ElementAnnotationsListener.php)
   was updated to properly handle input specs for elements and changed the default elements for datetime and date to
   Zend\Form\Element\DateTime and Zend\Form\Element\Date respectively.

# 0.4.0
Version `0.4.0` has been rewritten from scratch using the new ServiceManager component of ZF2. This allows for
drastically increased performance and reduced complexity of setup.

An alias has been set to reference Doctrine\ORM\EntityManager to help with BC but it is *deprecated and will be removed*
in the future. The new service name is doctrine.entitymanager.orm_default.

The module.doctrine_orm.local.config.php.dist has been removed. Please review the
[README.md](http://www.github.com/doctrine/DoctrineORMModule/tree/master/README.md) to setup your connection.

# 0.3.1
Version `0.3.1` integrated CLI with composer's CLI scripts registration method. To do so without conflicting with
Doctrine ORM's CLI, you now have to access it either via `./vendor/bin/doctrine-module` or
`./vendor/doctrine/DoctrineModule/bin/doctrine-module`.

# 0.3.0
After version `0.2.1`, submodules have been dropped as they were too heavy and complex to manage. If you now want to use
this module, please use composer, as described in
[README.md](http://www.github.com/doctrine/DoctrineORMModule/tree/master/README.md)
