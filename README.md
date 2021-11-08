Doctrine ORM Module for Laminas
===============================

[![Build Status](https://github.com/doctrine/DoctrineORMModule/actions/workflows/continuous-integration.yml/badge.svg)](https://github.com/doctrine/DoctrineORMModule/actions/workflows/continuous-integration.yml?query=branch%3A4.1.x)
[![Code Coverage](https://codecov.io/gh/doctrine/DoctrineORMModule/branch/4.1.x/graph/badge.svg)](https://codecov.io/gh/doctrine/DoctrineORMModule/branch/4.1.x)
[![Latest Stable Version](https://poser.pugx.org/doctrine/doctrine-orm-module/v/stable.png)](https://packagist.org/packages/doctrine/doctrine-orm-module)
[![Total Downloads](https://poser.pugx.org/doctrine/doctrine-orm-module/downloads.png)](https://packagist.org/packages/doctrine/doctrine-orm-module)

The DoctrineORMModule leverages [DoctrineModule](https://github.com/doctrine/DoctrineModule/) and integrates
[Doctrine ORM](https://github.com/doctrine/orm) with [Laminas](https://getlaminas.org/) quickly
and easily. The following features are intended to work out of the box: 

  - Doctrine ORM support
  - Multiple ORM entity managers
  - Multiple DBAL connections
  - Reuse existing PDO connections in DBAL connection

## Installation

Run the following to install this library using [Composer](https://getcomposer.org/):

```bash
composer require doctrine/doctrine-orm-module
```

### Note on PHP 8.0 or later

[DoctrineModule](https://github.com/doctrine/DoctrineModule/) provides an integration with 
[laminas-cache](https://docs.laminas.dev/laminas-cache/), which currently comes with some storage adapters which 
are not compatible with PHP 8.0 or later. To prevent installation of these unused cache adapters, you will need 
to add the following to your `composer.json` file:

```json
    "require": {
         "doctrine/doctrine-orm-module": "^4.1.0"
    },
    "replace": {
        "laminas/laminas-cache-storage-adapter-apc": "*",
        "laminas/laminas-cache-storage-adapter-dba": "*",
        "laminas/laminas-cache-storage-adapter-memcache": "*",
        "laminas/laminas-cache-storage-adapter-memcached": "*",
        "laminas/laminas-cache-storage-adapter-mongodb": "*",
        "laminas/laminas-cache-storage-adapter-wincache": "*",
        "laminas/laminas-cache-storage-adapter-xcache": "*",
        "laminas/laminas-cache-storage-adapter-zend-server": "*"
    }
```

Consult the [laminas-cache documentation](https://docs.laminas.dev/laminas-cache/installation/#avoid-unused-cache-adapters-are-being-installed)
for further information on this issue.

## Documentation

Please check the [documentation on the Doctrine website](https://www.doctrine-project.org/projects/doctrine-orm-module.html)
for more detailed information on features provided by this component. The source files for the documentation can be
found in the [docs directory](./docs/en).
