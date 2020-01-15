<?php

declare(strict_types=1);

namespace DoctrineORMModuleTest;

use DoctrineORMModule\ConfigProvider;
use PHPUnit\Framework\TestCase;

/**
 * Tests used to ensure ConfigProvider operates as expected
 *
 * @link    http://www.doctrine-project.org/
 */
class ConfigProviderTest extends TestCase
{
    public function testInvokeHasDependencyKeyAndNotServiceManager() : void
    {
        $config = (new ConfigProvider())->__invoke();

        self::assertArrayHasKey('dependencies', $config, 'Expected config to have "dependencies" array key');
        self::assertArrayNotHasKey('service_manager', $config, 'Config should not have "service_manager" array key');
    }
}
