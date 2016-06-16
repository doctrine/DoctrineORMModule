<?php

namespace DoctrineORMModule\Diagnostics;

use Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand;
use DoctrineModule\Component\Console\Input\RequestInput;
use DoctrineModule\Component\Console\Output\PropertyOutput;
use Zend\Console\Request;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CheckSchemaFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return CheckCommand
     * @throws \Zend\Console\Exception\RuntimeException
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var ValidateSchemaCommand $command */
        $command = $serviceLocator->get('doctrine.orm_cmd.validate_schema');
        
        $input = new RequestInput(new Request(array()));
        
        $output = new PropertyOutput();
        
        return new CheckCommand($command, $input, $output);
    }
}
