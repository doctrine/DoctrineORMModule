<?php
spl_autoload_register(include __DIR__ . '/autoload_function.php');

require_once(__DIR__ . '/Module.php');
$module = new DoctrineORMModule\Module();
Zend\Loader\AutoloaderFactory::factory($module->getAutoloaderConfig());