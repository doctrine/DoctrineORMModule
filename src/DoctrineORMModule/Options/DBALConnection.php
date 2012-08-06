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

namespace DoctrineORMModule\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * DBAL Connection options
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Kyle Spraggs <theman@spiffyjr.me>
 */
class DBALConnection extends AbstractOptions
{
    /**
     * Set the configuration key for the Configuration. Configuration key
     * is assembled as "doctrine.configuration.{key}" and pulled from
     * service locator.
     *
     * @var string
     */
    protected $configuration = 'orm_default';

    /**
     * Set the eventmanager key for the EventManager. EventManager key
     * is assembled as "doctrine.eventmanager.{key}" and pulled from
     * service locator.
     *
     * @var string
     */
    protected $eventmanager = 'orm_default';

    /**
     * Set the PDO instance, if any, to use. If a string is set
     * then the alias is pulled from the service locator.
     *
     * @var null|string|\PDO
     */
    protected $pdo = null;

    /**
     * Setting the driver is deprecated. You should set the
     * driver class directly instead.
     *
     * @var string
     */
    protected $driverClass = 'Doctrine\DBAL\Driver\PDOMySql\Driver';

    /**
     * Set the wrapper class for the driver. In general, this shouldn't
     * need to be changed.
     *
     * @var string|null
     */
    protected $wrapperClass = null;

    /**
     * Driver specific connection parameters.
     *
     * @var array
     */
    protected $params = array();

    /**
     * @var array
     */
    protected $doctrineTypeMappings = array();

    /**
     * @param string $configuration
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return string
     */
    public function getConfiguration()
    {
        return "doctrine.configuration.{$this->configuration}";
    }

    /**
     * @param string $eventmanager
     */
    public function setEventmanager($eventmanager)
    {
        $this->eventmanager = $eventmanager;
    }

    /**
     * @return string
     */
    public function getEventmanager()
    {
        return "doctrine.eventmanager.{$this->eventmanager}";
    }

    /**
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param  array                                     $doctrineTypeMappings
     * @return \DoctrineORMModule\Options\DBALConnection
     */
    public function setDoctrineTypeMappings($doctrineTypeMappings)
    {
        $this->doctrineTypeMappings = (array) $doctrineTypeMappings;

        return $this;
    }

    /**
     *
     * @return array
     */
    public function getDoctrineTypeMappings()
    {
        return $this->doctrineTypeMappings;
    }

    /**
     * @param null|string $driverClass
     */
    public function setDriverClass($driverClass)
    {
        $this->driverClass = $driverClass;
    }

    /**
     * @return null|string
     */
    public function getDriverClass()
    {
        return $this->driverClass;
    }

    /**
     * @param null|\PDO|string $pdo
     */
    public function setPdo($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return null|\PDO|string
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * @param string $wrapperClass
     */
    public function setWrapperClass($wrapperClass)
    {
        $this->wrapperClass = $wrapperClass;
    }

    /**
     * @return string
     */
    public function getWrapperClass()
    {
        return $this->wrapperClass;
    }
}
