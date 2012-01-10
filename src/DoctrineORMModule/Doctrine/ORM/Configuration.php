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
 * and is licensed under the LGPL. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace DoctrineORMModule\Doctrine\ORM;

use Doctrine\Common\Cache\Cache,
    Doctrine\DBAL\Logging\SQLLogger,
    Doctrine\ORM\Configuration as DoctrineConfiguration,
    DoctrineModule\Doctrine\Instance;

/**
 * Wrapper for Doctrine ORM configuration that helps setup configuration without relying
 * entirely on Di.
 *
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link    www.doctrine-project.org
 * @since   1.0
 * @version $Revision$
 * @author  Kyle Spraggs <theman@spiffyjr.me>
 */
class Configuration extends Instance
{
    /**
     * Definition for configuration options. 
     * 
     * @var array
     */
    protected $definition = array(
        'required' => array(
            'auto_generate_proxies' => 'boolean',
            'proxy_dir'             => 'string',
            'proxy_namespace'       => 'string',
        ),
        'optional' => array(
            'entity_namespaces'         => 'array',
            'custom_datetime_functions' => 'array',
            'custom_numeric_functions'  => 'array',
            'custom_string_functions'   => 'array',
            'custom_hydration_modes'    => 'array',
            'named_queries'             => 'array',
            'named_native_queries'      => 'array',
        )
    );
    
    /**
     * @var Doctrine\ORM\Mapping\Driver\Driver
     */
    protected $metadataDriver;
    
    /**
     * @var Doctrine\Common\Cache\Cache
     */
    protected $metadataCache;

    /**
     * @var Doctrine\Common\Cache\Cache
     */
    protected $queryCache;
    
    /**
     * @var Doctrine\Common\Cache\Cache
     */
    protected $resutlCache;
    
    /**
     * @var Doctrine\DBAL\Logging\SQLLogger
     */
    protected $logger;
    
    /**
     * Constructor.
     * 
     * @param array 	$opts
     * @param Cache 	$metadataCache
     * @param Cache 	$queryCache
     * @param Cache 	$resultCache
     * @param SQLLogger $logger
     */
    public function __construct(array $opts, $metadataDriver, Cache $metadataCache, Cache $queryCache, 
                                Cache $resultCache = null, SQLLogger $logger = null)
    {
    	if ($metadataDriver instanceof DriverChain) {
    		$metadataDriver = $metadataDriver->getInstance();
    	}
    	
    	$this->metadataDriver = $metadataDriver;
        $this->metadataCache  = $metadataCache;
        $this->queryCache     = $queryCache;
        $this->resultCache    = $resultCache;
        $this->logger         = $logger;
        
        parent::__construct($opts);
    }
    
    /**
     * (non-PHPdoc)
     * @see DoctrineORMModule\Instance.Instance::loadInstance()
     */
    protected function loadInstance()
    {
        $opts   = $this->opts;
        $config = new DoctrineConfiguration;
        
        // proxies
        $config->setAutoGenerateProxyClasses($opts['auto_generate_proxies']);
        $config->setProxyDir($opts['proxy_dir']);
        $config->setProxyNamespace($opts['proxy_namespace']);

        // entity namespaces
        $config->setEntityNamespaces($opts['entity_namespaces']);

        // add custom functions
        $config->setCustomDatetimeFunctions($opts['custom_datetime_functions']);
        $config->setCustomStringFunctions($opts['custom_string_functions']);
        $config->setCustomNumericFunctions($opts['custom_numeric_functions']);
        
        // custom queries
        foreach($opts['named_queries'] as $query) {
            $this->_validateOptions($query, $this->_namedQueryDefinition);
            $config->addNamedQuery($query['name'], $query['dql']);
        }
        
        foreach($opts['named_native_queries'] as $query) {
            $this->_validateOptions($query, $this->_namedNativeQueryDefinition);
            $config->addNamedNativeQuery($query['name'], $query['sql'], new $query['rsm']);
        }

        // caching
        $config->setQueryCacheImpl($this->queryCache);
        $config->setMetadataCacheImpl($this->metadataCache);
        $config->setResultCacheImpl($this->metadataCache);

        // logger
        $config->setSQLLogger($this->logger);
        
        // finally, the driver
        $config->setMetadataDriverImpl($this->metadataDriver);
        
        $this->instance = $config;
    }
}