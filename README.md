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
[getcomposer.org](http://getcomposer.org/). To achieve the task, it currently uses ocramius/OcraComposer to integrate
your application with composer. This may change in future.

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
  3. install composer via `curl -s http://getcomposer.org/installer | php` (on windows, download
     http://getcomposer.org/installer and execute it with PHP)
  4. run `php composer.phar install`
  5. open `my/project/directory/configs/application.config.php` and add following keys to your `modules` (in this order)

     ```php
     'OcraComposer',
     'DoctrineModule',
     'DoctrineORMModule',
     ```

     also add following `module_paths`:

     ```php
     'vendor/ocramius',
     'vendor/doctrine',
     ```

  6. drop `vendor/doctrine/DoctrineModule/config/module.doctrine_orm.local.config.php.dist` into your application's
     `config/autoload` directory, rename it to `module.doctrine_orm.local.config.php` and make the appropriate changes.
  8. create directory `my/project/directory/data/DoctrineORMModule/Proxy` and make sure your application has write
     access to it.

## Usage

#### Command Line
Access the Doctrine command line as following

```sh
./vendor/doctrine/DoctrineModule/bin/doctrine
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