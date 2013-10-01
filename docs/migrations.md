#### How to configure Doctrine Migrations

```php
return array(    
    'doctrine' => array(
        'migrations_configuration' => array(
            'orm_default' => array(
                'directory' => 'path/to/migrations/dir',
                'name' => 'Migrations Name',
                'namespace' => 'Migrations  Namespace',
                'table' => 'migrations_table',
            ),
        ),
    ),
)
```