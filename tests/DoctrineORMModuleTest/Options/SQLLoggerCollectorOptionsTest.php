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
use DoctrineORMModule\Options\SQLLoggerCollectorOptions;

class SQLLoggerCollectorOptionsTest extends TestCase
{
    public function testSetGetSQLLogger()
    {
        $options = new SQLLoggerCollectorOptions();
        $options->setSqlLogger('sql-logger-name');
        $this->assertSame('sql-logger-name', $options->getSqlLogger());
        $options->setSqlLogger(null);
        $this->assertSame(null, $options->getSqlLogger());
    }

    public function testSetGetConfiguration()
    {
        $options = new SQLLoggerCollectorOptions();
        $options->setConfiguration('configuration-name');
        $this->assertSame('configuration-name', $options->getConfiguration());
        $options->setConfiguration(null);
        $this->assertSame('doctrine.configuration.orm_default', $options->getConfiguration());
    }

    public function testSetGetName()
    {
        $options = new SQLLoggerCollectorOptions();
        $this->assertSame('orm_default', $options->getName()); // testing defaults too!
        $options->setName('collector-name');
        $this->assertSame('collector-name', $options->getName());
        $options->setName(null);
        $this->assertSame('', $options->getName());
    }
}
