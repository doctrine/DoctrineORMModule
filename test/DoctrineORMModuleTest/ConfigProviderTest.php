<?php

namespace DoctrineORMModuleTest;

use DoctrineORMModule\ConfigProvider;
use PHPUnit\Framework\TestCase;

/**
 * Tests used to ensure ConfigProvider operates as expected
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  James Titcumb <james@asgrim.com>
 */
class ConfigProviderTest extends TestCase
{
    public function testInvokeHasDependencyKeyAndNotServiceManager()
    {
        $config = (new ConfigProvider())->__invoke();

        self::assertArrayHasKey('dependencies', $config, 'Expected config to have "dependencies" array key');
        self::assertArrayNotHasKey('service_manager', $config, 'Config should not have "service_manager" array key');
    }
}
