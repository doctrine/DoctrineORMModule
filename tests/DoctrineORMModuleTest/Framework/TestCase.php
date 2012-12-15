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
use Zend\ServiceManager\ServiceManager;
use DoctrineORMModuleTest\Util\ServiceManagerFactory;

/**
 * Base test case for tests using the entity manager
 */
class TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var boolean
     */
    protected $hasDb = false;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Creates a database if not done already.
     */
    public function createDb()
    {
        if ($this->hasDb) {
            return;
        }

        $em   = $this->getEntityManager();
        $tool = new SchemaTool($em);
        $tool->updateSchema($em->getMetadataFactory()->getAllMetadata());
        $this->hasDb = true;
    }

    /**
     * Drops existing database
     */
    public function dropDb()
    {
        $em   = $this->getEntityManager();
        $tool = new SchemaTool($em);
        $tool->dropSchema($em->getMetadataFactory()->getAllMetadata());
        $em->clear();

        $this->hasDb = false;
    }

    /**
     * Get EntityManager.
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        if ($this->entityManager) {
            return $this->entityManager;
        }

        $serviceManager = ServiceManagerFactory::getServiceManager();
        $serviceManager->get('doctrine.entity_resolver.orm_default');
        $this->entityManager = $serviceManager->get('doctrine.entitymanager.orm_default');

        return $this->entityManager;
    }
}
