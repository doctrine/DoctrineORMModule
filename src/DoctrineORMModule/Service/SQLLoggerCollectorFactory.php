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

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use RuntimeException;

use DoctrineORMModule\Collector\SQLLoggerCollector;

use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\DBAL\Logging\LoggerChain;

/**
 * DBAL Configuration ServiceManager factory
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Marco Pivetta <ocramius@gmail.com>
 */
class SQLLoggerCollectorFactory implements FactoryInterface
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
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var $options \DoctrineORMModule\Options\SQLLoggerCollectorOptions */
        $options = $this->getOptions($serviceLocator);

        // @todo always ask the serviceLocator instead? (add a factory?)
        if ($options->getSqlLogger()) {
            $debugStackLogger = $serviceLocator->get($options->getSqlLogger());
        } else {
            $debugStackLogger = new DebugStack();
        }

        /* @var $configuration \Doctrine\ORM\Configuration */
        $configuration = $serviceLocator->get($options->getConfiguration());

        if (null !== $configuration->getSQLLogger()) {
            $logger = new LoggerChain();
            $logger->addLogger($debugStackLogger);
            $logger->addLogger($configuration->getSQLLogger());
            $configuration->setSQLLogger($logger);
        } else {
            $configuration->setSQLLogger($debugStackLogger);
        }

        return new SQLLoggerCollector($debugStackLogger, $options->getName());
    }

    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @return mixed
     * @throws RuntimeException
     */
    protected function getOptions(ServiceLocatorInterface $serviceLocator)
    {
        $options = $serviceLocator->get('Config');
        $options = $options['doctrine'];
        $options = isset($options['sql_logger_collector'][$this->name])
            ? $options['sql_logger_collector'][$this->name]
            : null;

        if (null === $options) {
            throw new RuntimeException(sprintf(
                'Configuration with name "%s" could not be found in "doctrine.sql_logger_collector".',
                $this->name
            ));
        }

        $optionsClass = $this->getOptionsClass();

        return new $optionsClass($options);
    }

    /**
     * {@inheritDoc}
     */
    protected function getOptionsClass()
    {
        return 'DoctrineORMModule\Options\SQLLoggerCollectorOptions';
    }
}
