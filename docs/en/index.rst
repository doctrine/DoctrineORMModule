Introduction
============

The DoctrineORMModule leverages `DoctrineModule <https://www.doctrine-project.org/projects/doctrine-module/en/current/index.html>`__
and integrates `Doctrine ORM <https://www.doctrine-project.org/projects/doctrine-orm/en/current/index.html>`__
with `Laminas <https://getlaminas.org/>`__ quickly and easily. The following features are intended
to work out of the box:

  - Doctrine ORM support
  - Multiple ORM entity managers
  - Multiple DBAL connections
  - Reuse existing PDO connections in DBAL connection

Installation
------------

Run the following to install this library using `Composer <https://getcomposer.org/>`__:

.. code:: bash

   $ composer require doctrine/doctrine-orm-module

Note on PHP 8.0 or later
^^^^^^^^^^^^^^^^^^^^^^^^

`DoctrineModule <https://www.doctrine-project.org/projects/doctrine-module/en/current/index.html>`__
provides an integration with `laminas-cache <https://docs.laminas.dev/laminas-cache/>`__, which
currently comes with some storage adapters which are not compatible with PHP 8.0 or later. To
prevent installation of these unused cache adapters, you will need to add the following to your
``composer.json`` file:

.. code:: json

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

Consult the `laminas-cache documentation <https://docs.laminas.dev/laminas-cache/installation/#avoid-unused-cache-adapters-are-being-installed>`__
for further information on this issue.

Next Steps
----------

.. toctree::
    :caption: Table of Contents

    user-guide
    developer-tools
    configuration
    cache
    migrations
    forms
    miscellaneous
