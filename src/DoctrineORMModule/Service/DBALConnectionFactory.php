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
use DoctrineModule\Service\AbstractFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * DBAL Connection ServiceManager factory
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Kyle Spraggs <theman@spiffyjr.me>
 */
class DBALConnectionFactory extends AbstractFactory
{
    /**
     * {@inheritDoc}
     * @return \Doctrine\DBAL\Connection
     */
    public function createService(ServiceLocatorInterface $sl)
    {
        /** @var $options \DoctrineORMModule\Options\DBALConnection */
        $options = $this->getOptions($sl, 'connection');
        $pdo     = $options->getPdo();

        if (is_string($pdo)) {
            $pdo = $sl->get($pdo);
        }

        $params = array(
            'driverClass'  => $options->getDriverClass(),
            'wrapperClass' => $options->getWrapperClass(),
            'pdo'          => $pdo,
        );
        $params = array_merge($params, $options->getParams());

        $configuration = $sl->get($options->getConfiguration());
        $eventManager  = $sl->get($options->getEventManager());

        $connection = DriverManager::getConnection($params, $configuration, $eventManager);
        $platform = $connection->getDatabasePlatform();
        foreach ($options->getDoctrineTypeMappings() as $dbType => $doctrineType) {
            $platform->registerDoctrineTypeMapping($dbType, $doctrineType);
        }

        return $connection;
    }

    /**
     * Get the class name of the options associated with this factory.
     *
     * @return string
     */
    public function getOptionsClass()
    {
        return 'DoctrineORMModule\Options\DBALConnection';
    }
}
