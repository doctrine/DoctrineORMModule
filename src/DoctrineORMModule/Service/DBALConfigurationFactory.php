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

use RuntimeException;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Types\Type;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * DBAL Configuration ServiceManager factory
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Kyle Spraggs <theman@spiffyjr.me>
 */
class DBALConfigurationFactory implements FactoryInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritDoc}
     * @return \Doctrine\DBAL\Configuration
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config  = new Configuration();
        $this->setupDBALConfiguration($serviceLocator, $config);

        return $config;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param Configuration           $config
     */
    public function setupDBALConfiguration(ServiceLocatorInterface $serviceLocator, Configuration $config)
    {
        $options = $this->getOptions($serviceLocator);
        $config->setResultCacheImpl($serviceLocator->get($options->resultCache));

        $sqlLogger = $options->sqlLogger;
        if (is_string($sqlLogger) and $serviceLocator->has($sqlLogger)) {
            $sqlLogger = $serviceLocator->get($sqlLogger);
        }
        $config->setSQLLogger($sqlLogger);

        foreach ($options->types as $name => $class) {
            if (Type::hasType($name)) {
                Type::overrideType($name, $class);
            } else {
                Type::addType($name, $class);
            }
        }
    }

    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @return mixed
     * @throws RuntimeException
     */
    public function getOptions(ServiceLocatorInterface $serviceLocator)
    {
        $options = $serviceLocator->get('Config');
        $options = $options['doctrine'];
        $options = isset($options['configuration'][$this->name]) ? $options['configuration'][$this->name] : null;

        if (null === $options) {
            throw new RuntimeException(sprintf(
                'Configuration with name "%s" could not be found in "doctrine.configuration".',
                $this->name
            ));
        }

        $optionsClass = $this->getOptionsClass();

        return new $optionsClass($options);
    }

    /**
     * @return string
     */
    protected function getOptionsClass()
    {
        return 'DoctrineModule\Options\Configuration';
    }
}
