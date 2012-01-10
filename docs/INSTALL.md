# Installing the Doctrine ORM module for Zend Framework 2 
The simplest way to install is to clone the repository into your /vendor directory add the 
DoctrineORMModule key to your modules array before your Application module key.

  1. cd my/project/folder
  2. git clone git://github.com/doctrine/DoctrineORMModule.git vendor/DoctrineORMModule --recursive
  3. open my/project/folder/configs/application.config.php and add 'DoctrineORMModule' to your 'modules' parameter.
  4. drop config/module.doctrine_orm.config.php.dist into your application config/autoload folder
     and make the appropriate changes.
     
## Usage
Access the entity manager using the following di alias: 

    $em = $this->getLocator()->get('doctrine_em');