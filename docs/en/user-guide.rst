User Guide
==========

Installation of this module uses composer. For composer documentation,
please refer to `getcomposer.org <http://getcomposer.org/>`__.

.. code:: sh

   composer require doctrine/doctrine-orm-module

Then add ``DoctrineModule`` and ``DoctrineORMModule`` to your
``config/application.config.php`` and create directory
``data/DoctrineORMModule/Proxy`` and make sure your application has
write access to it.

Installation without composer is not officially supported and requires
you to manually install all dependencies that are listed in
``composer.json``

Entities settings
-----------------

To register your entities with the ORM, add following metadata driver
configurations to your module (merged) configuration for each of your
entities namespaces:

.. code:: php

   <?php
   return [
       'doctrine' => [
           'driver' => [
               // defines an annotation driver with two paths, and names it `my_annotation_driver`
               'my_annotation_driver' => [
                   'class' => \Doctrine\ORM\Mapping\Driver\AnnotationDriver::class,
                   'cache' => 'array',
                   'paths' => [
                       'path/to/my/entities',
                       'another/path',
                   ],
               ],

               // default metadata driver, aggregates all other drivers into a single one.
               // Override `orm_default` only if you know what you're doing
               'orm_default' => [
                   'drivers' => [
                       // register `my_annotation_driver` for any entity under namespace `My\Namespace`
                       'My\Namespace' => 'my_annotation_driver',
                   ],
               ],
           ],
       ],
   ];

Connection settings
-------------------

Connection parameters can be defined in the application configuration:

.. code:: php

   <?php
   return [
       'doctrine' => [
           'connection' => [
               // default connection name
               'orm_default' => [
                   'driverClass' => \Doctrine\DBAL\Driver\PDO\MySQL\Driver::class,
                   'params' => [
                       'host'     => 'localhost',
                       'port'     => '3306',
                       'user'     => 'username',
                       'password' => 'password',
                       'dbname'   => 'database',
                   ],
               ],
           ],
       ],
   ];

Full configuration options
^^^^^^^^^^^^^^^^^^^^^^^^^^

An exhaustive list of configuration options can be found directly in the
Options classes of each module.

-  `DoctrineModule
   configuration <https://github.com/Doctrine/DoctrineModule/tree/master/src/DoctrineModule/Options>`__
-  `ORM Module
   Configuration <https://github.com/Doctrine/DoctrineORMModule/tree/master/src/DoctrineORMModule/Options>`__
-  `ORM Module
   Defaults <https://github.com/Doctrine/DoctrineORMModule/tree/master/config/module.config.php>`__

You can find documentation about the module’s features at the following
links:

-  `DoctrineModule
   documentation <https://github.com/Doctrine/DoctrineModule/tree/master/docs>`__
-  `DoctrineORMModule
   documentation <https://github.com/Doctrine/DoctrineORMModule/tree/master/docs>`__

Registered Service names
------------------------

-  ``doctrine.connection.orm_default``: a ``Doctrine\DBAL\Connection``
   instance
-  ``doctrine.configuration.orm_default``: a
   ``Doctrine\ORM\Configuration`` instance
-  ``doctrine.driver.orm_default``: default mapping driver instance
-  ``doctrine.entitymanager.orm_default``: the
   ``Doctrine\ORM\EntityManager`` instance
-  ``Doctrine\ORM\EntityManager``: an alias of
   ``doctrine.entitymanager.orm_default``
-  ``doctrine.eventmanager.orm_default``: the
   ``Doctrine\Common\EventManager`` instance

Command Line
^^^^^^^^^^^^

Access the Doctrine command line as following

.. code:: sh

   ./vendor/bin/doctrine-module

Service Locator
^^^^^^^^^^^^^^^

To access the entity manager, use the main service locator:

.. code:: php

   // for example, in a controller:
   $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
   $em = $this->getServiceLocator()->get(\Doctrine\ORM\EntityManager::class);
