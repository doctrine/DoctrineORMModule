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

namespace DoctrineORMModule\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Service factory for migrations command
 *
 * @license MIT
 * @author Aleksandr Sandrovskiy <a.sandrovsky@gmail.com>
 */
class MigrationsCommandFactory implements FactoryInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = ucfirst(strtolower($name));
    }

    /**
     * {@inheritDoc}
     *
     * @return \Doctrine\Migrations\Tools\Console\Command\AbstractCommand
     * @throws \InvalidArgumentException
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $className = 'Doctrine\Migrations\Tools\Console\Command\\' . $this->name . 'Command';

        if (! class_exists($className)) {
            throw new \InvalidArgumentException();
        }

        // @TODO currently hardcoded: `orm_default` should be injected
        /* @var $configuration \Doctrine\Migrations\Configuration\Configuration */
        $configuration = $container->get('doctrine.migrations_configuration.orm_default');
        /* @var $command \Doctrine\Migrations\Tools\Console\Command\AbstractCommand */
        $command       = new $className;

        $command->setMigrationConfiguration($configuration);

        return $command;
    }

    /**
     * @param \Zend\ServiceManager\ServiceLocatorInterface $container
     * @return \Doctrine\Migrations\Tools\Console\Command\AbstractCommand
     * @throws \InvalidArgumentException
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, 'Doctrine\Migrations\Tools\Console\Command\\' . $this->name . 'Command');
    }
}
