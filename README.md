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
  4. open `my/project/directory/config/application.config.php` and add `DoctrineModule` and `DoctrineORMModule` to your `modules`
  5. create directory `my/project/directory/data/DoctrineORMModule/Proxy` and make sure your application has write
     access to it. This directory can be changed using the module options.

#### Registering drivers with the DriverChain

To register drivers with Doctrine module simply add the drivers to the doctrine.driver key in your configuration.

```php
<?php
return array(
    'doctrine' => array(
        'driver' => array(
            'my_annotation_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array('path/to/my/entities', 'another/path/if/i/want')
            )
        )
    )
);
```

By default, this module ships with a DriverChain so that modules can add their entities to the chain. Once you have setup
your driver you should add it to the chain as follows:

```php
<?php
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
chain however you wish and/or add drivers to it directly without using the driver factory and configuration array. A
good place to do this is the `onBootstrap()` method of your `Module.php` file or in another service.

#### Setting up your connection

Setup your connection by adding the module configuration to any valid ZF2 config file. This can be any file in autoload/
or a module configuration (such as the Application/config/module.config.php file).

```php
<?php
return array(
    'doctrine' => array(
        'connection' => array(
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

You can add more connections by adding additional keys to the `connection` and specifying your parameters.

#### Full configuration options

An exhaustive list of configuration options can be found directly in the Options classes of each module.

 * [Common configuration](https://github.com/Doctrine/DoctrineModule/tree/master/src/DoctrineModule/Options)
 * [ORM Configuration](https://github.com/Doctrine/DoctrineORMModule/tree/master/src/DoctrineORMModule/Options)

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
Access the entity manager using the following alias:

```php
<?php
$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
```

#### Injection
You can also inject the `EntityManager` directly in your controllers/services by using a controller factory. Please
refer to the official ServiceManager documentation for more information.

#### Implementing EntityManagerAware interface
Coming soon!
