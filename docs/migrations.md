#### How to configure Doctrine Migrations

```php
return [
    'doctrine' => [
        'migrations_configuration' => [
            'orm_default' => [
                'directory' => 'path/to/migrations/dir',
                'name' => 'Migrations Name',
                'namespace' => 'Migrations  Namespace',
                'table' => 'migrations_table',
                'column' => 'version',
            ],
        ],
    ],
];
```
