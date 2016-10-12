# 1.1.0
- [#484](https://github.com/doctrine/DoctrineORMModule/pull/484) Remove 0.x hint for composer installs.
- [#488](https://github.com/doctrine/DoctrineORMModule/pull/488) Fix: Streamline configuration examples
- [#493](https://github.com/doctrine/DoctrineORMModule/pull/493) ZF3 compatibility
- [#494](https://github.com/doctrine/DoctrineORMModule/pull/494) Hotfix router config
- [#498](https://github.com/doctrine/DoctrineORMModule/pull/498) Expose to `zend-component-installer` as module `DoctrineORMModule`
- [#500](https://github.com/doctrine/DoctrineORMModule/pull/500) ZF3 Composer dependencies - hotfix
- [#496](https://github.com/doctrine/DoctrineORMModule/pull/496) Fix: Missing view helper "zendDeveloperToolsTime"
- [#492](https://github.com/doctrine/DoctrineORMModule/pull/492) Update to EXTRA_ORM.md to correct config key
- [#505](https://github.com/doctrine/DoctrineORMModule/pull/505) Require stable version of DoctrineModule 1.2.0 instead of dev-master

# 1.0.0

# 0.11.0
 * [#459](https://github.com/doctrine/DoctrineORMModule/pull/459) Added ServiceManager v3 support
 * [#460](https://github.com/doctrine/DoctrineORMModule/pull/460) Add .gitattributes to remove unneeded files
 * [#453](https://github.com/doctrine/DoctrineORMModule/pull/453) Added an option to configure the ORM quote strategy

# 0.10.0

 * [#450](https://github.com/doctrine/DoctrineORMModule/pull/450) Use stable release for DoctrineModule
 * [#443](https://github.com/doctrine/DoctrineORMModule/pull/443) Added ability to configure the version column in MigrationsConfigurationFactory
 * [#457](https://github.com/doctrine/DoctrineORMModule/pull/457) Fixed compatibility with Zend\Mvc 2.7
 * [#458](https://github.com/doctrine/DoctrineORMModule/pull/458) Drop PHP 5.4 and allow PHP 7 on Travis

# 0.9.2
 * [#423](https://github.com/doctrine/DoctrineORMModule/pull/423) Docs about cache updated
 * [#428](https://github.com/doctrine/DoctrineORMModule/pull/428) metadatagrapher - fix diagram inconcistency
 * [#429](https://github.com/doctrine/DoctrineORMModule/pull/429) Added naming strategy doc
 * [#434](https://github.com/doctrine/DoctrineORMModule/pull/434) bump orm to 2.5 dependency
 * [#435](https://github.com/doctrine/DoctrineORMModule/pull/435) Feature/metadatagrapher fix
 * [#437](https://github.com/doctrine/DoctrineORMModule/pull/437) Doctrine comment types

# 0.9.1
 * [#405](https://github.com/doctrine/DoctrineORMModule/pull/405) Provide the dialog and question helper
 * [#413](https://github.com/doctrine/DoctrineORMModule/pull/413) Fixed missing version
 * [#414](https://github.com/doctrine/DoctrineORMModule/pull/414) Change test configuration to manage migrations path
 * [#417](https://github.com/doctrine/DoctrineORMModule/pull/417) Bump some dependencies versions to be ZF 2.5 compliant
 * [#418](https://github.com/doctrine/DoctrineORMModule/pull/418) Change build paremeters to prevent errors with hhvm and php7

# 0.9.0
 * [#199](https://github.com/doctrine/DoctrineORMModule/pull/199) Add 'entity_listener_resolver' config key
 * [#281](https://github.com/doctrine/DoctrineORMModule/pull/281) Forced failing unit test for #247
 * [#306](https://github.com/doctrine/DoctrineORMModule/pull/306) Removing PHP 5.3.3 support
 * [#272](https://github.com/doctrine/DoctrineORMModule/pull/272) Fix and test #270, #242, #285
 * [#311](https://github.com/doctrine/DoctrineORMModule/pull/311) remove unused statement
 * [#326](https://github.com/doctrine/DoctrineORMModule/pull/326) Highlight only uppercase words
 * [#329](https://github.com/doctrine/DoctrineORMModule/pull/329) remove unused statements
 * [#328](https://github.com/doctrine/DoctrineORMModule/pull/328) wrong var assignment
 * [#313](https://github.com/doctrine/DoctrineORMModule/pull/313) Prevent overriding of type
 * [#338](https://github.com/doctrine/DoctrineORMModule/pull/338) order the classes
 * [#346](https://github.com/doctrine/DoctrineORMModule/pull/346) Corrected a typo in a comment to better clarify
 * [#357](https://github.com/doctrine/DoctrineORMModule/pull/357) Modify sql_logger_collector class factory
 * [#360](https://github.com/doctrine/DoctrineORMModule/pull/360) Add example for entity_resolver
 * [#359](https://github.com/doctrine/DoctrineORMModule/pull/359) Update deprecated dialog console helper
 * [#363](https://github.com/doctrine/DoctrineORMModule/pull/363) Prevent Zend\Form\Element\File types inherit of StringLength validator...
 * [#365](https://github.com/doctrine/DoctrineORMModule/pull/365) Re-enable scrutinizer code coverage
 * [#373](https://github.com/doctrine/DoctrineORMModule/pull/373) Add doc for cache
 * [#347](https://github.com/doctrine/DoctrineORMModule/pull/347) added extra check in handleRequiredField
 * [#377](https://github.com/doctrine/DoctrineORMModule/pull/377) fix docblocks
 * [#376](https://github.com/doctrine/DoctrineORMModule/pull/376) Add latest migrations command
 * [#375](https://github.com/doctrine/DoctrineORMModule/pull/375) Default repository
 * [#374](https://github.com/doctrine/DoctrineORMModule/pull/374) Use `ResolveTargetEntityListener` as an event subscriber when supported
 * [#318](https://github.com/doctrine/DoctrineORMModule/pull/318) Add support for second level cache
 * [#378](https://github.com/doctrine/DoctrineORMModule/pull/378) Allow to set file lock for SLC
 * [#380](https://github.com/doctrine/DoctrineORMModule/pull/380) Fix typo in configuration file markdown.
 * [#385](https://github.com/doctrine/DoctrineORMModule/pull/385) Allow symfony 3.0 components
 * [#388](https://github.com/doctrine/DoctrineORMModule/pull/388) update comment block in Module.php as no Module::getAutoloaderConfig()
 * [#389](https://github.com/doctrine/DoctrineORMModule/pull/389) Delete Module.php in root directory
 * [#390](https://github.com/doctrine/DoctrineORMModule/pull/390) travis: PHP 5.6, 7.0 nightly added, 5.3 dropped
 * [#392](https://github.com/doctrine/DoctrineORMModule/pull/392) Use-case: caching module' s configuration
 * [#396](https://github.com/doctrine/DoctrineORMModule/pull/396) Removed unnecessary line in travis config
 * [#398](https://github.com/doctrine/DoctrineORMModule/pull/398) Composer * -update for stable version

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
