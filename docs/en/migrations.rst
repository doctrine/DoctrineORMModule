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
                        'version_column_length' => 191,
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

Set a Custom configuration into DependencyFactory
-------------------------------

.. code:: php

    return [
        'doctrine' => [
            'migrations_configuration' => [
                'orm_default' => [
                    'dependency_factory_services' => [
                        'service_to_overwrite' => 'custom_service_id'
                    ],
                ],
            ],
        ],
    ];

Note : 'custom_service_id' has to be defined in your DIC


This configuration allows you, for example, to define a custom version comparator

.. code:: php

    return [
        'doctrine' => [
            'migrations_configuration' => [
                'orm_default' => [
                    'dependency_factory_services' => [
                        \Doctrine\Migrations\Version\Comparator::class => MyComparator::class
                    ],
                ],
            ],
        ],
    ];

List of services that can be overwritten
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

- Doctrine\\Migrations\\Finder\\MigrationFinder
- Doctrine\\Migrations\\Metadata\\Storage\\MetadataStorage
- Doctrine\\Migrations\\MigrationsRepository
- Doctrine\\Migrations\\Provider\\SchemaProvider
- Doctrine\\Migrations\\Tools\\Console\\MigratorConfigurationFactory
- Doctrine\\Migrations\\Version\\Comparator
- Doctrine\\Migrations\\Version\\MigrationFactory
- Doctrine\\Migrations\\Version\\MigrationPlanCalculator
- Doctrine\\Migrations\\Version\\MigrationStatusCalculator
- Psr\\Log\\LoggerInterface
- Symfony\\Component\\Stopwatch\\Stopwatch