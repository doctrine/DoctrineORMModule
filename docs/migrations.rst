Doctrine Migrations
===================

Support for the migrations library ^3.0 is included.  You may create one
migration configuration for each object manager.
See the `module documentation <https://www.doctrine-project.org/projects/doctrine-migrations/en/3.0/index.html>`__ for
more information.

Configure
---------

.. code:: php

    return [
        'doctrine' => [
            'migrations_configuration' => [
                'orm_default' => [
                    'table_storage' => [
                        'table_name' => 'DoctrineMigrationVersions',
                        'version_column_name' => 'version',
                        'version_column_length' => 1024,
                        'executed_at_column_name' => 'executedAt',
                        'execution_time_column_name' => 'executionTime',
                    ],
                    'migrations_paths' => [], // an array of namespace => path
                    'migrations' => [], // an array of fully qualified migrations
                    'all_or_nothing' => false,
                    'check_database_platform' => true,
                    'organize_migrations' => 'year', // year or year_and_month
                    'custom_template' => null,
                ],
                'orm_other' => [
                    ...
                ]
            ],
        ],
    ];
