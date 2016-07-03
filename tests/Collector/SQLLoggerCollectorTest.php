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

namespace DoctrineORMModuleTest\Collector;

use PHPUnit_Framework_TestCase as TestCase;
use DoctrineORMModule\Collector\SQLLoggerCollector;
use Doctrine\DBAL\Logging\DebugStack;
use Zend\Mvc\MvcEvent;

class SQLLoggerCollectorTest extends TestCase
{
    /**
     * @var DebugStack
     */
    protected $logger;

    /**
     * @var string
     */
    protected $name = 'test-collector-name';

    /**
     * @var SQLLoggerCollector
     */
    protected $collector;

    public function setUp()
    {
        parent::setUp();
        $this->logger = new DebugStack();
        $this->collector = new SQLLoggerCollector($this->logger, $this->name);
    }

    public function testHasCorrectName()
    {
        $this->assertSame($this->name, $this->collector->getName());
    }

    public function testGetPriority()
    {
        $this->assertInternalType('int', $this->collector->getPriority());
    }

    public function testCollect()
    {
        $this->collector->collect(new MvcEvent());
    }

    public function testCanHide()
    {
        $this->assertTrue($this->collector->canHide());
        $this->logger->startQuery('some sql');
        $this->assertFalse($this->collector->canHide());
    }

    public function testGetQueryCount()
    {
        $this->assertSame(0, $this->collector->getQueryCount());
        $this->logger->startQuery('some sql');
        $this->assertSame(1, $this->collector->getQueryCount());
        $this->logger->startQuery('some more sql');
        $this->assertSame(2, $this->collector->getQueryCount());
    }

    public function testGetQueryTime()
    {
        $start = microtime(true);
        $this->assertEquals(0, $this->collector->getQueryTime());

        $this->logger->startQuery('some sql');
        $this->logger->stopQuery();
        $time = microtime(true) - $start;
        $time1 = $this->collector->getQueryTime();
        $this->assertGreaterThan(0, $time1);
        $this->assertLessThan($time, $time1);

        $this->logger->startQuery('some more sql');
        $this->logger->stopQuery();
        $time = microtime(true) - $start;
        $time2 = $this->collector->getQueryTime();
        $this->assertGreaterThan($time1, $time2);
        $this->assertLessThan($time, $time1);
    }
}
