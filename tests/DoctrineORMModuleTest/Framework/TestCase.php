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

namespace DoctrineORMModuleTest\Framework;

use PHPUnit_Framework_TestCase;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use Zend\ServiceManager\ServiceManager;

class TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceManager
     */
    protected static $sm;

    /**
     * @var boolean
     */
    protected static $hasDb = false;

    /**
     * Creates a database if not done already.
     */
    public function createDb()
    {
        if (self::$hasDb) {
            return;
        }

        $em = $this->getEntityManager();
        $tool = new SchemaTool($em);
        $tool->updateSchema($em->getMetadataFactory()->getAllMetadata());
        self::$hasDb = true;
    }

    public function dropDb()
    {
        $em = $this->getEntityManager();
        $tool = new SchemaTool($em);
        $tool->dropSchema($em->getMetadataFactory()->getAllMetadata());
        $em->clear();
        self::$hasDb = false;
    }

    /**
     * @param ServiceManager $sm
     */
    public static function setServiceManager(ServiceManager $sm)
    {
        self::$sm = $sm;
    }

    /**
     * @return ServiceManager
     */
    public function getServiceManager()
    {
    	return self::$sm;
    }

    /**
     * Get EntityManager.
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->getServiceManager()->get('doctrine.entitymanager.orm_default');
    }
}
