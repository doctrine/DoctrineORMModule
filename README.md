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

To register drivers with the driver chain simply include the following snippet in your Module's init() method.

     ```php
     $sharedEvents = $mm->events()->getSharedManager();
     $sharedEvents->attach('DoctrineORMModule', 'loadDrivers', function($e) {
         return array(
             'Application\Entity' => $e->getParam('config')->newDefaultAnnotationDriver('/src/Application/Entity')
         );
     });
     ```

In the example above the newDefaultAnnotationDriver() method is used to create a generic annotation driver. You have
full control over the number and types of drivers to use. The only requirement is that the driver is returned as an
array with the namespace as the key and the driver as the value.

## Usage

#### Command Line
Access the Doctrine command line as following

```sh
./vendor/bin/doctrine-module
```