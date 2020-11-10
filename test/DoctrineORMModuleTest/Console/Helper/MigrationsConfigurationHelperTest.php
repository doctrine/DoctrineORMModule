<?php
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

namespace DoctrineORMModuleTest\Console\Helper;

use DoctrineModule\Component\Console\Input\RequestInput;
use DoctrineORMModule\Console\Helper\MigrationsConfigurationHelper;
use DoctrineORMModuleTest\ServiceManagerFactory;
use Laminas\Console\Request;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

/**
 * Tests for {@see \DoctrineORMModule\Service\MigrationsCommandFactory}
 *
 * @covers \DoctrineORMModule\Service\MigrationsCommandFactory
 */
class MigrationsConfigurationHelperTest extends TestCase
{
    /** @var ServiceManager */
    private $serviceLocator;

    public function setUp(): void
    {
        $this->serviceLocator = ServiceManagerFactory::getServiceManager();
    }

    public function testCreate(): void
    {
        $helper = new MigrationsConfigurationHelper($this->serviceLocator);

        $this->assertInstanceOf(
            MigrationsConfigurationHelper::class,
            $helper
        );
    }

    public function testGetDefaultMigrationConfig(): void
    {
        $request       = new Request();
        $requestInput  = new RequestInput($request);
        $helper        = new MigrationsConfigurationHelper($this->serviceLocator);
        $configuration = $helper->getMigrationConfig($requestInput);

        $this->assertEquals(
            $this->serviceLocator->get('doctrine.migrations_configuration.orm_default'),
            $configuration
        );
    }

    public function testGetNonDefaultMigrationConfig(): void
    {
        $inputOption     = new InputOption('object-manager', [], InputOption::VALUE_REQUIRED);
        $inputDefinition = new InputDefinition([$inputOption]);
        $request         = new Request([
            'index.php',
            '--object-manager=doctrine.entitymanager.orm_some_other_name',
        ]);
        $requestInput    = new RequestInput($request, $inputDefinition);
        $helper          = new MigrationsConfigurationHelper($this->serviceLocator);

        try {
            $configuration = $helper->getMigrationConfig($requestInput);
        } catch (ServiceNotFoundException $e) {
            $this->assertEquals('Unable to resolve service '
                . '"doctrine.migrations_configuration.orm_some_other_name" to '
                . 'a factory; are you certain you provided it during configuration?', $e->getMessage());
        }
    }
}
