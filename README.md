# Doctrine 2 ORM Module for Zend Framework 2

[![Master branch build status](https://secure.travis-ci.org/doctrine/DoctrineORMModule.png?branch=master)](http://travis-ci.org/doctrine/DoctrineORMModule) [![Latest Stable Version](https://poser.pugx.org/doctrine/doctrine-orm-module/v/stable.png)](https://packagist.org/packages/doctrine/doctrine-orm-module) [![Total Downloads](https://poser.pugx.org/doctrine/doctrine-orm-module/downloads.png)](https://packagist.org/packages/doctrine/doctrine-orm-module) 

DoctrineORMModule integrates Doctrine 2 ORM with Zend Framework 2 quickly and easily.

  - Doctrine 2 ORM support
  - Multiple ORM entity managers
  - Multiple DBAL connections
  - Reuse existing PDO connections in DBAL connection

## Installation

Installation of this module uses composer. For composer documentation, please refer to
[getcomposer.org](http://getcomposer.org/).

```sh
php composer.phar require doctrine/doctrine-orm-module
# (When asked for a version, type `0.*`)
```

Then add `DoctrineModule` and `DoctrineORMModule` to your `config/application.config.php` and create directory
`data/DoctrineORMModule/Proxy` and make sure your application has write access to it.

Installation without composer is not officially supported and requires you to manually install all dependencies
that are listed in `composer.json`

## Entities settings

To register your entities with the ORM, add following metadata driver configurations to your module (merged)
configuration for each of your entities namespaces:

```php
<?php
return array(
    'doctrine' => array(
        'driver' => array(
            // defines an annotation driver with two paths, and names it `my_annotation_driver`
            'my_annotation_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    'path/to/my/entities',
                    'another/path'
                ),
            ),

            // default metadata driver, aggregates all other drivers into a single one.
            // Override `orm_default` only if you know what you're doing
            'orm_default' => array(
                'drivers' => array(
                    // register `my_annotation_driver` for any entity under namespace `My\Namespace`
                    'My\Namespace' => 'my_annotation_driver'
                )
            )
        )
    )
);
```

## Connection settings

Connection parameters can be defined in the application configuration:

```php
<?php
return array(
    'doctrine' => array(
        'connection' => array(
            // default connection name
            'orm_default' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'params' => array(
                    'host'     => 'localhost',
                    'port'     => '3306',
                    'user'     => 'username',
                    'password' => 'password',
                    'dbname'   => 'database',
                )
            )
        )
    ),
);
```

#### Full configuration options

An exhaustive list of configuration options can be found directly in the Options classes of each module.

 * [DoctrineModule configuration](https://github.com/Doctrine/DoctrineModule/tree/master/src/DoctrineModule/Options)
 * [ORM Module Configuration](https://github.com/Doctrine/DoctrineORMModule/tree/master/src/DoctrineORMModule/Options)
 * [ORM Module Defaults](https://github.com/Doctrine/DoctrineORMModule/tree/master/config/module.config.php)

You can find documentation about the module's features at the following links:

 * [DoctrineModule documentation](https://github.com/Doctrine/DoctrineModule/tree/master/docs)
 * [DoctrineORMModule documentation](https://github.com/Doctrine/DoctrineORMModule/tree/master/docs)

## Registered Service names

 * `doctrine.connection.orm_default`: a `Doctrine\DBAL\Connection` instance
 * `doctrine.configuration.orm_default`: a `Doctrine\ORM\Configuration` instance
 * `doctrine.driver.orm_default`: default mapping driver instance
 * `doctrine.entitymanager.orm_default`: the `Doctrine\ORM\EntityManager` instance
 * `Doctrine\ORM\EntityManager`: an alias of `doctrine.entitymanager.orm_default`
 * `doctrine.eventmanager.orm_default`: the `Doctrine\Common\EventManager` instance

#### Command Line
Access the Doctrine command line as following

```sh
./vendor/bin/doctrine-module
```

#### Service Locator
To access the entity manager, use the main service locator:

```php
// for example, in a controller:
$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
$em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
```
