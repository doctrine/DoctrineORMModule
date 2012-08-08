# Zend Developer Tools in DoctrineORMModule

If you ever tried [Zend Developer Tools](https://github.com/zendframework/ZendDeveloperTools) you will surely understand
the importance of being able to track performance pitfalls or excessive amount of queries in your applications when
developing.

## Setup

To setup [Zend Developer Tools](https://github.com/zendframework/ZendDeveloperTools), run

```sh
php composer.phar require zendframework/zend-developer-tools
```

Then enable `ZendDeveloperTools` in your modules and enable profiling and the toolbar (see docs of Zend Developer Tools
for that).

Once `ZendDeveloperTools` is enabled, having `doctrine.entity_manager.orm_default` as your default `EntityManager`, you
will notice that the queries performed by the ORM get logged and displayed in the toolbar.

![](http://github.com/doctrine/DoctrineORMModule/raw/master/docs/images/zf2-zend-developer-tools-doctrine-module.png)

## Customization

If you want to customize this behavior (or track multiple `EntityManager` instances) you can do it in different ways.
Please note that if you have set an `SQLLogger` in your configuration, this functionality won't override it, so you can
use these features in total safety.

### Multiple EntityManager/Connection instances and logging

*WARNING! These are advanced features! Even if the code is fully tested, this is usually not required for most users!*

To setup logging for an additional DBAL Connection or EntityManager, put something like following in your module:

```php
<?php

namespace MyNamespace;

class Module
{
    public function getConfig()
    {
        return array(
            'doctrine' => array(
                'sql_logger_collector' => array(
                    'other_orm' => array(
                        // name of the sql logger collector (used by ZendDeveloperTools)
                        'name' => 'other_orm',

                        // name of the configuration service at which to attach the logger
                        'configuration' => 'doctrine.configuration.other_orm',

                        // uncomment following if you want to use a particular SQL logger instead of relying on
                        // the attached one
                        //'sql_logger' => 'service_name_of_my_dbal_sql_logger',
                    ),
                ),
            ),

            'zenddevelopertools' => array(

                // registering the profiler with ZendDeveloperTools
                'profiler' => array(
                    'collectors' => array(
                        // reference to the service we have defined
                        'other_orm' => 'doctrine.sql_logger_collector.other_orm',
                    ),
                ),

                // registering a new toolbar item with ZendDeveloperTools (name must be the same of the collector name)
                'toolbar' => array(
                    'entries' => array(
                        // this is actually a name of a view script to use - you can use your custom one
                        'other_orm' => 'zend-developer-tools/toolbar/doctrine-orm',
                    ),
                ),
            ),
        );
    }

    public function getServiceConfiguration()
    {
        return array(
            'factories' => array(
                // defining a service (any name is valid as long as you use it consistently across this example)
                'doctrine.sql_logger_collector.other_orm' => new \DoctrineORMModule\Service\SQLLoggerCollectorFactory('other_orm'),
            ),
        );
    }

    public function onBootstrap(\Zend\EventManager\EventInterface $e)
    {
        $config = $e->getTarget()->getServiceManager()->get('Config');

        if (
            isset($config['zenddevelopertools']['profiler']['enabled']) 
            && $config['zenddevelopertools']['profiler']['enabled']
        ) {
            // when ZendDeveloperTools is enabled, initialize the sql collector
            $app->getServiceManager()->get('doctrine.sql_logger_collector.other_orm');
        }
    }
}
```

This example will simply generate a new icon in the toolbar, with the log results of your `other_orm` connection:


![](http://github.com/doctrine/DoctrineORMModule/raw/master/docs/images/zend-developer-tools-multiple-entity-managers.png)
