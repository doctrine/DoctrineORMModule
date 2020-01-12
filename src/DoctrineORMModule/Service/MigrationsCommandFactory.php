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

namespace DoctrineORMModule\Service;

use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Tools\Console\Command\AbstractCommand;
use Interop\Container\ContainerInterface;
use InvalidArgumentException;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use function assert;
use function class_exists;
use function strtolower;
use function ucfirst;

/**
 * Service factory for migrations command
 */
class MigrationsCommandFactory implements FactoryInterface
{
    private string $name;

    /**
     * {@inheritDoc}
     */
    public function __construct($name)
    {
        $this->name = ucfirst(strtolower($name));
    }

    /**
     * {@inheritDoc}
     *
     * @return AbstractCommand
     *
     * @throws InvalidArgumentException
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $className = 'Doctrine\Migrations\Tools\Console\Command\\' . $this->name . 'Command';

        if (! class_exists($className)) {
            throw new InvalidArgumentException();
        }

        $configuration = $container->get('doctrine.migrations_configuration.orm_default');
        // @TODO currently hardcoded: `orm_default` should be injected
        assert($configuration instanceof Configuration);
        $command = new $className();
        assert($command instanceof AbstractCommand);

        $command->setMigrationConfiguration($configuration);

        return $command;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function createService(ServiceLocatorInterface $container) : AbstractCommand
    {
        return $this($container, 'Doctrine\Migrations\Tools\Console\Command\\' . $this->name . 'Command');
    }
}
