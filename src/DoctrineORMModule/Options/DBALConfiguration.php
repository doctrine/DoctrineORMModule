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
 * Configuration options for a DBAL Connection
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Kyle Spraggs <theman@spiffyjr.me>
 */
class DBALConfiguration extends AbstractOptions
{
    /**
     * Set the cache key for the result cache. Cache key
     * is assembled as "doctrine.cache.{key}" and pulled from
     * service locator.
     *
     * @var string
     */
    protected $resultCache = 'array';

    /**
     * Set the class name of the SQL Logger, or null, to disable.
     *
     * @var string
     */
    protected $sqlLogger = null;

    /**
     * Keys must be the name of the type identifier and value is
     * the class name of the Type
     *
     * @var array
     */
    protected $types = array();

    /**
     * @param string $resultCache
     */
    public function setResultCache($resultCache)
    {
        $this->resultCache = $resultCache;
    }

    /**
     * @return string
     */
    public function getResultCache()
    {
        return 'doctrine.cache.' . $this->resultCache;
    }

    /**
     * @param string $sqlLogger
     */
    public function setSqlLogger($sqlLogger)
    {
        $this->sqlLogger = $sqlLogger;
    }

    /**
     * @return string
     */
    public function getSqlLogger()
    {
        return $this->sqlLogger;
    }

    /**
     * @param array $types
     */
    public function setTypes(array $types)
    {
        $this->types = $types;
    }

    /**
     * @return string
     */
    public function getTypes()
    {
        return $this->types;
    }
}
