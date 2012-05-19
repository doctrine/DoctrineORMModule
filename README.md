# DoctrineORM Module for Zend Framework 2

Master: [![Build Status](https://secure.travis-ci.org/doctrine/DoctrineORMModule.png?branch=master)](http://travis-ci.org/doctrine/DoctrineORMModule)

The DoctrineORMModule module intends to integrate Doctrine 2 ORM with Zend Framework 2 quickly
and easily. The following features are intended to work out of the box:

  - Doctrine ORM support
  - Multiple ORM entity managers
  - Multiple DBAL connections
  - Support reuse existing PDO connections in DBAL

## Requirements
[Zend Framework 2](http://www.github.com/zendframework/zf2)

## Installation

Installation of this module uses composer. For composer documentation, please refer to
[getcomposer.org](http://getcomposer.org/).

#### Installation steps

  1. `cd my/project/directory`
  2. create a `composer.json` file with following contents:

     ```json
     {
         "require": {
             "doctrine/DoctrineORMModule": "dev-master"
         }
     }
     ```
  3. run `php composer.phar install`
  4. open `my/project/directory/configs/application.config.php` and add `DoctrineORMModule` to your `modules`
  5. drop `vendor/doctrine/DoctrineORMModule/config/module.doctrine_orm.local.config.php.dist` into your application's
     `config/autoload` directory, rename it to `module.doctrine_orm.local.config.php` and make the appropriate changes.
  6. create directory `my/project/directory/data/DoctrineORMModule/Proxy` and make sure your application has write
     access to it.

## Registering drivers with the DriverChain

To register drivers with the driver chain simply add the necessary configuration options to your configuration.

```
return array(
    'doctrine' => array(
        'driver' => array(
            'my_annotation_driver' => array(
                'type'  => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    'path/to/my/entities'
                )
            )
        )
    )
);
```

By default, the orm ships with a DriverChain so that modules can add their entities to the chain. Once you have setup
your driver you should add it to the chain as follows:

```
return array(
    'doctrine' => array(
        'driver' => array(
            'orm_default' => array(
                'drivers' => array(
                    'My\Namespace' => 'my_annotation_driver'
                )
            )
        )
    )
);
```

You also have access to the chain directly via the `doctrine.driver.orm_default` service and you can manipulate the
chain however you wish and/or add drivers to it directly without using the driver factory and configuration array.

#### Custom driver settings

Certain drivers have custom configuration options.

 * FileDrivers, can take extension as an option to modify the file extension.
 * AnnotationDriver, can take cache as an option to specify the cache to use from the doctrine.cache array.
 * DriverChain, can take drivers as an option to specify the drivers to load from the doctrine.driver array.

## Usage

#### Registered Services

 * doctrine.connection.orm_default
 * doctrine.configuration.orm_default
 * doctrine.driver.orm_default
 * doctrine.entitymanager.orm_default
 * doctrine.eventmanager.orm_default

#### Command Line
Access the Doctrine command line as following

```sh
./vendor/bin/doctrine-module
```

#### Service Locator
Access the entity manager using the following di alias:

```php
<?php
$em = $this->getLocator()->get('Doctrine\ORM\EntityManager');
```

#### Injection
You can also inject the `EntityManager` directly in your controllers/services:
```php
class MyController extends \Zend\Mvc\Controller\ActionController
{
    public function __construct(\Doctrine\ORM\EntityManager $em) {
        $this->em = $em;
        // now you can use the EntityManager!
    }
}
