# Installing the Doctrine ORM module for Zend Framework 2 

## Git Submodule installation

The simplest way to install is to clone the repository into your /vendor directory add the 
DoctrineORMModule key to your modules array before your Application module key.

  1. `cd my/project/directory`
  2. `git clone git://github.com/doctrine/DoctrineORMModule.git vendor/DoctrineORMModule --recursive`
  3. open `my/project/directory/configs/application.config.php` and add `DoctrineORMModule` to your `modules` parameter.
  4. drop `config/module.doctrine_orm.local.config.php.dist` into your application `config/autoload` directory,
     rename to `module.doctrine_orm.local.config.php` and make the appropriate changes.
  5. do a `mkdir -p my/project/directory/data/DoctrineORMModule/Proxy` and make sure your application has write access.

## Composer installation

Composer installation brings you the advantages of composer in your ZF2 project. It is tested, but it currently
uses an unofficial `OcraComposer` package, which is still quite primitive.

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
  4. open `my/project/directory/configs/application.config.php` and add following keys to your `modules` (in this order)
     
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
     
  5. drop `config/module.doctrine_orm.local.config.php.dist` into your application `config/autoload` directory,
     rename it to `module.doctrine_orm.local.config.php` and make the appropriate changes.
  6. run `php composer.phar install`
  7. see http://getcomposer.org to understand what composer can do for you
     
## Usage
Access the entity manager using the following di alias: 

```php
$em = $this->getLocator()->get('Doctrine\ORM\EntityManager');
```
