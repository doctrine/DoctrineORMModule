Doctrine Migrations
===================

Support for the migrations library is included.  Only one
migration configuration is possible.

Configure
---------

.. code:: php

    return [
        'doctrine' => [
            'migrations_configuration' => [
                'orm_default' => [
                    'directory' => 'path/to/migrations/dir',
                    'name' => 'Migrations Name',
                    'namespace' => 'Migrations  Namespace',
                    'table' => 'migrations_table',
                    'column' => 'version',
                    'custom_template' => null,
                ],
            ],
        ],
    ];


Multiple Migration Configurations
---------------------------------

At this time if you want to have migrations for multiple entity manager database configurations
you must use the `.phar archive <https://github.com/doctrine/migrations/releases>`_ and
external configuration files.
