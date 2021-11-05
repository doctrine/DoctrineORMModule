<?php

declare(strict_types=1);

namespace DoctrineORMModuleTest\Service;

use Doctrine\DBAL\Tools\Console\Command\RunSqlCommand;
use DoctrineORMModule\Service\RunSqlCommandFactory;
use DoctrineORMModuleTest\ServiceManagerFactory;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

/**
 * @covers \DoctrineORMModule\Service\RunSqlCommandFactory
 */
class RunSqlCommandFactoryTest extends TestCase
{
    /** @var ServiceManager */
    private $serviceLocator;

    public function setUp(): void
    {
        $this->serviceLocator = ServiceManagerFactory::getServiceManager();
    }

    public function testCreateCommand(): void
    {
        $factory = new RunSqlCommandFactory();

        $this->assertInstanceOf(
            RunSqlCommand::class,
            $factory->createService($this->serviceLocator)
        );
    }
}
