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

namespace DoctrineORMModuleTest\Di;

use DoctrineORMModuleTest\Framework\TestCase;
use Doctrine\DBAL\Connection;

class ConnectionTest extends TestCase
{
    /**
     * Verifying that the connection that is injected into a custom object is actually the same that can be retrieved
     * directly or from the EntityManager in the locator
     */
    public function testCanInjectConnection()
    {
        $locator = $this->getLocator();
        /* @var $target ConnectionTestInjectTarget */
        $target = $locator->get('DoctrineORMModuleTest\\Di\\ConnectionTestInjectTarget');
        $this->assertInstanceOf('DoctrineORMModuleTest\\Di\\ConnectionTestInjectTarget', $target);
        $connection = $target->getConnection();
        $this->assertInstanceOf('Doctrine\\DBAL\\Connection', $connection);
        $this->assertSame($connection, $locator->get('Doctrine\\DBAL\\Connection'));
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $locator->get('Doctrine\\ORM\\EntityManager');
        $this->assertSame($connection, $em->getConnection());
    }
}

class ConnectionTestInjectTarget
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
