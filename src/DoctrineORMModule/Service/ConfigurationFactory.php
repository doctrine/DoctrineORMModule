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

use DoctrineORMModule\Service\DBALConfigurationFactory as DoctrineConfigurationFactory;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\Exception\InvalidArgumentException;
use Doctrine\ORM\Configuration;

class ConfigurationFactory extends DoctrineConfigurationFactory
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var $options \DoctrineORMModule\Options\Configuration */
        $options = $this->getOptions($serviceLocator);
        $config  = new Configuration();

        $config->setAutoGenerateProxyClasses($options->getGenerateProxies());
        $config->setProxyDir($options->getProxyDir());
        $config->setProxyNamespace($options->getProxyNamespace());

        $config->setEntityNamespaces($options->getEntityNamespaces());

        $config->setCustomDatetimeFunctions($options->getDatetimeFunctions());
        $config->setCustomStringFunctions($options->getStringFunctions());
        $config->setCustomNumericFunctions($options->getNumericFunctions());

        foreach ($options->getNamedQueries() as $name => $query) {
            $config->addNamedQuery($name, $query);
        }

        foreach ($options->getNamedNativeQueries() as $name => $query) {
            $config->addNamedNativeQuery($name, $query['sql'], new $query['rsm']);
        }

        foreach ($options->getCustomHydrationModes() AS $modeName => $hydrator) {
            $config->addCustomHydrationMode($modeName, $hydrator);
        }

        foreach ($options->getFilters() as $name => $class) {
            $config->addFilter($name, $class);
        }

        $config->setMetadataCacheImpl($serviceLocator->get($options->getMetadataCache()));
        $config->setQueryCacheImpl($serviceLocator->get($options->getQueryCache()));
        $config->setResultCacheImpl($serviceLocator->get($options->getResultCache()));
        $config->setMetadataDriverImpl($serviceLocator->get($options->getDriver()));

        if ($namingStrategy = $options->getNamingStrategy()) {
            if (is_string($namingStrategy)) {
                if (!$serviceLocator->has($namingStrategy)) {
                    throw new InvalidArgumentException(sprintf(
                        'Naming strategy "%s" not found',
                        $namingStrategy
                    ));
                }

                $config->setNamingStrategy($serviceLocator->get($namingStrategy));
            } else {
                $config->setNamingStrategy($namingStrategy);
            }
        }

        $this->setupDBALConfiguration($serviceLocator, $config);

        return $config;
    }

    protected function getOptionsClass()
    {
        return 'DoctrineORMModule\Options\Configuration';
    }
}
