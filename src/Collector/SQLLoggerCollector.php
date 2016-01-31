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

namespace DoctrineORMModule\Collector;

use ZendDeveloperTools\Collector\CollectorInterface;
use ZendDeveloperTools\Collector\AutoHideInterface;

use Zend\Mvc\MvcEvent;

use Doctrine\DBAL\Logging\DebugStack;

/**
 * Collector to be used in ZendDeveloperTools to record and display SQL queries
 *
 * @license MIT
 * @link    www.doctrine-project.org
 * @author  Marco Pivetta <ocramius@gmail.com>
 */
class SQLLoggerCollector implements CollectorInterface, AutoHideInterface
{
    /**
     * Collector priority
     */
    const PRIORITY = 10;

    /**
     * @var DebugStack
     */
    protected $sqlLogger;

    /**
     * @var string
     */
    protected $name;

    /**
     * @param DebugStack $sqlLogger
     * @param string     $name
     */
    public function __construct(DebugStack $sqlLogger, $name)
    {
        $this->sqlLogger = $sqlLogger;
        $this->name = (string) $name;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getPriority()
    {
        return static::PRIORITY;
    }

    /**
     * {@inheritDoc}
     */
    public function collect(MvcEvent $mvcEvent)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function canHide()
    {
        return empty($this->sqlLogger->queries);
    }

    /**
     * @return int
     */
    public function getQueryCount()
    {
        return count($this->sqlLogger->queries);
    }

    /**
     * @return array
     */
    public function getQueries()
    {
        return $this->sqlLogger->queries;
    }

    /**
     * @return float
     */
    public function getQueryTime()
    {
        $time = 0.0;

        foreach ($this->sqlLogger->queries as $query) {
            $time += $query['executionMS'];
        }

        return $time;
    }
}
