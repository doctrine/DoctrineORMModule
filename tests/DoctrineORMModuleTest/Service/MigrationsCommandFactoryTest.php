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

namespace DoctrineORMModuleTest\Service;

use DoctrineORMModule\Service\MigrationsCommandFactory;
use PHPUnit_Framework_TestCase as TestCase;
use DoctrineORMModuleTest\Util\ServiceManagerFactory;

/**
 * Tests for {@see \DoctrineORMModule\Service\MigrationsCommandFactory}
 *
 * @license MIT
 * @author Aleksandr Sandrovskiy <a.sandrovsky@gmail.com>
 *
 * @covers \DoctrineORMModule\Service\MigrationsCommandFactory
 */
class MigrationsCommandFactoryTest extends TestCase
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    private $serviceLocator;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->serviceLocator = ServiceManagerFactory::getServiceManager();
    }

    public function testExecuteFactory()
    {
        $factory = new MigrationsCommandFactory('execute');

        $this->assertInstanceOf(
            'Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand',
            $factory->createService($this->serviceLocator)
        );
    }

    public function testDiffFactory()
    {
        $factory = new MigrationsCommandFactory('diff');

        $this->assertInstanceOf(
            'Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand',
            $factory->createService($this->serviceLocator)
        );
    }

    public function testThrowException()
    {
        $factory = new MigrationsCommandFactory('unknowncommand');

        $this->setExpectedException('InvalidArgumentException');
        $factory->createService($this->serviceLocator);
    }
}
