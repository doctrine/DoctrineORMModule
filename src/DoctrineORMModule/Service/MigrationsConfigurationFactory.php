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
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace DoctrineORMModule\Service;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Migrations\Configuration\Configuration;
use DoctrineModule\Service\AbstractFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * DBAL Connection ServiceManager factory
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Marco Pivetta <ocramius@gmail.com>
 */
class MigrationsConfigurationFactory extends AbstractFactory
{
    /**
     * {@inheritDoc}
     *
     * @return \Doctrine\DBAL\Migrations\Configuration\Configuration
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $name             = $this->getName();
        /* @var $connection \Doctrine\DBAL\Connection */
        $connection       = $serviceLocator->get('doctrine.connection.' . $name);
        $appConfig        = $serviceLocator->get('Config');
        $migrationsConfig = $appConfig['doctrine']['migrations_configuration'][$name];
        $configuration    = new Configuration($connection);

        $configuration->setName($migrationsConfig['name']);
        $configuration->setMigrationsDirectory($migrationsConfig['directory']);
        $configuration->setMigrationsNamespace($migrationsConfig['namespace']);
        $configuration->setMigrationsTableName($migrationsConfig['table']);
        $configuration->registerMigrationsFromDirectory($migrationsConfig['directory']);

        return $configuration;
    }

    /**
     * {@inheritDoc}
     */
    public function getOptionsClass()
    {
    }
}
