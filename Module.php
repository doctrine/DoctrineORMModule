<?php

namespace SpiffyDoctrineORM;

use Doctrine\Common\Annotations\AnnotationRegistry,
    Zend\Module\Consumer\AutoloaderProvider;

class Module implements AutoloaderProvider
{
    public function init()
    {
        $libfile = __DIR__ . '/vendor/doctrine2-orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php';
        if (file_exists($libfile)) {
            AnnotationRegistry::registerFile($libfile);
        } else {
            @include_once 'Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php';
            if (!class_exists('Doctrine\ORM\Mapping\Entity', false)) {
                throw new \Exception(
                    'Ensure Doctrine can be autoloaded or initalize submodules in SpiffyDoctrineORM'
                );
            }
        }
    }
    
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
        );
    }

    public function getConfig($env = null)
    {
        return include __DIR__ . '/config/module.config.php';
    }
}