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
 * Configuration options for an collector
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Marco Pivetta <ocramius@gmail.com>
 */
class SQLLoggerCollectorOptions extends AbstractOptions
{
    /**
     * @var string name to be assigned to the collector
     */
    protected $name = 'orm_default';

    /**
     * @var string|null service name of the configuration where the logger has to be put
     */
    protected $configuration;

    /**
     * @var string|null service name of the SQLLogger to be used
     */
    protected $sqlLogger;

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = (string) $name;
    }

    /**
     * Name of the collector
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string|null $configuration
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration ? (string) $configuration : null;
    }

    /**
     * Configuration service name (where to set the logger)
     *
     * @return string
     */
    public function getConfiguration()
    {
        return $this->configuration ? $this->configuration : 'doctrine.configuration.orm_default';
    }

    /**
     * @param string|null $sqlLogger
     */
    public function setSqlLogger($sqlLogger)
    {
        $this->sqlLogger = $sqlLogger ? (string) $sqlLogger : null;
    }

    /**
     * SQLLogger service name
     *
     * @return string|null
     */
    public function getSqlLogger()
    {
        return $this->sqlLogger;
    }
}
