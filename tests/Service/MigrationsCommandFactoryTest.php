<?php

declare(strict_types=1);

/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace DoctrineORMModuleTest\Service;

use Doctrine\Migrations\Tools\Console\Command\DiffCommand;
use Doctrine\Migrations\Tools\Console\Command\ExecuteCommand;
use Doctrine\Migrations\Tools\Console\Command\VersionCommand;
use Doctrine\ORM\EntityManagerInterface;
use DoctrineORMModule\Service\MigrationsCommandFactory;
use DoctrineORMModuleTest\ServiceManagerFactory;
use InvalidArgumentException;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use stdClass;

use function class_exists;

/**
 * Tests for {@see \DoctrineORMModule\Service\MigrationsCommandFactory}
 *
 * @covers \DoctrineORMModule\Service\MigrationsCommandFactory
 */
class MigrationsCommandFactoryTest extends TestCase
{
    private ServiceManager $serviceLocator;

    public function setUp(): void
    {
        $this->serviceLocator = ServiceManagerFactory::getServiceManager();
    }

    public function testExecuteFactory(): void
    {
        if (! class_exists(VersionCommand::class)) {
            $this->markTestIncomplete(
                'Migrations must be installed to run this test.'
            );
        }

        $factory = new MigrationsCommandFactory('execute');

        $this->assertInstanceOf(
            ExecuteCommand::class,
            $factory($this->serviceLocator, ExecuteCommand::class)
        );
    }

    public function testDiffFactory(): void
    {
        if (! class_exists(VersionCommand::class)) {
            $this->markTestIncomplete(
                'Migrations must be installed to run this test.'
            );
        }

        $factory = new MigrationsCommandFactory('diff');

        $this->assertInstanceOf(
            DiffCommand::class,
            $factory($this->serviceLocator, DiffCommand::class)
        );
    }

    public function testThrowException(): void
    {
        $factory = new MigrationsCommandFactory('unknowncommand');

        $this->expectException(InvalidArgumentException::class);
        $factory($this->serviceLocator, stdClass::class);
    }

    public function testDefineDependencyFactoryServicesFromConfig(): void
    {
        if (! class_exists(VersionCommand::class)) {
            $this->markTestIncomplete(
                'Migrations must be installed to run this test.'
            );
        }

        $factory        = new MigrationsCommandFactory('diff');
        $config         = [
            'doctrine' => [
                'migrations_configuration' => [
                    'orm_default' => [
                        'dependency_factory_services' => ['myId' => 'myService'],
                    ],
                ],
            ],
        ];
        $entityManager  = self::createMock(EntityManagerInterface::class);
        $serviceLocator = self::createMock(ServiceManager::class);
        $serviceLocator->expects(self::exactly(3))
            ->method('get')
            ->willReturnMap([
                ['config', $config],
                ['doctrine.entitymanager.orm_default', $entityManager],
                ['myService', 'test'],
            ]);

        $factory($serviceLocator, DiffCommand::class);
    }

    public function testNoDefineDependencyFactoryServicesFromConfig(): void
    {
        if (! class_exists(VersionCommand::class)) {
            $this->markTestIncomplete(
                'Migrations must be installed to run this test.'
            );
        }

        $factory        = new MigrationsCommandFactory('diff');
        $config         = [
            'doctrine' => [
                'migrations_configuration' => [
                    'orm_default' => [],
                ],
            ],
        ];
        $entityManager  = self::createMock(EntityManagerInterface::class);
        $serviceLocator = self::createMock(ServiceManager::class);
        $serviceLocator->expects(self::exactly(2))
            ->method('get')
            ->willReturnMap([
                ['config', $config],
                ['doctrine.entitymanager.orm_default', $entityManager],
            ]);

        $factory($serviceLocator, DiffCommand::class);
    }
}
