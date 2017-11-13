Migrations
==========

Migrations are sets of database commands which move your database schema forward and backward along with your
code base.  Each migration has an `up()` and `down()` function and can accept raw SQL or `$schema` functions
to dynamically build the database.

You may configure multiple migrations; one for each object manager in your application.  For each object
manager you may specify one directory where migrations are stored.  By convention migrations are named
based on the time they are created and thus self-organize when sorted by name.  This convention avoids
migrations with duplicate names too.

Even though you may configure multiple migrations and you can have multiple object managers per database
it is not recommended to have more than one migrations directory per database.  However this unusual arrangement
is possible.

> In prior versions of doctrine/doctrine-orm-module only one migrations configuration
> was supported.  Because of this change this module will have a new major revision when this is released.



Configuration
-------------

```php
return [
    'doctrine' => [
        'migrations' => [
            'doctrine.entitymanager.orm_default' => [ // The service manager alias for the database
                'directory' => 'path/to/migrations/dir',
                'name' => 'Default',
                'namespace' => 'Migrations\Namespace',
                'table' => 'Migrations',
                'column' => 'version',
            ],
            'doctrine.entitymanager.orm_other_object_manager' => [
                'directory' => __DIR__ . '/other/migrations/path',
                'name' => 'zf-doctrine-audit',
                'namespace' => 'Other\ObjectManager\Migrations\Namespace',
                'table' => 'Migrations',
                'column' => 'version',
            ],
            ...
        ],
    ],
];
```

### 'directory'
This option is a fully qualified path to a directory where migrations are stored.  It is best to use a relative
directory and prefix it with `__DIR__` to get the current working directory.

### 'name'
The name of the migrations.  This is for reference only.

### 'namespace'
The namespace of the migrations.  Used when generating new migrations.

### 'table'
The table name to store the list of migrations which have been ran on the database for the object manager.
Works with `'version'`

### 'version'
The field name in the `'table'` to store migrations which have been ran for the object manager's database.


Use
---

Migrations are handled from the command line and there are several commands available.  For each command an optional
`--object-manager=` option is available.  If no value is given for this option then the default
`doctrine.entitymanager.orm_default` will be used.


### index.php migrations:status

View the status of a set of migrations.


### index.php migrations:latest

Outputs the latest version number


### index.php migrations:generate

Execute a migration to a specified version or the latest available version.


### index.php migrations.diff

Generate a migration by comparing your current database to your mapping information.


### index.php migrations:execute

Execute a single migration version up or down manually.


### index.php migrations:generate

Generate a blank migration class.


### index.php migrations:version

Manually add and delete migration versions from the version table.
