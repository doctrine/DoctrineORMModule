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

namespace DoctrineORMModuleTest\Service;

use DoctrineORMModuleTest\Framework\TestCase;
use DoctrineORMModule\Service\EntityManagerInitializer;
use PHPUnit_Framework_TestCase;
use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\ServiceManager;
use DoctrineORMModuleTest\Util\ServiceManagerFactory;

class EntityManagerInitializerTest extends TestCase
{
    public function testActualServiceManagerWillInitializeEntityManager()
    {        
        $serviceManager = ServiceManagerFactory::getServiceManager();
        $serviceManager->get('doctrine.entity_resolver.orm_default');
        $serviceManager->setInvokableClass('object-manager-aware', 'DoctrineORMModuleTest\Service\ObjectManagerAwareDummy');
        
        $entityManager = $serviceManager->get('doctrine.entitymanager.orm_default');        
        
        $this->assertSame($entityManager, $serviceManager->get('object-manager-aware')->getObjectManager());
    }
    
    public function testServiceManagerWillInitializeEntityManager()
    {
        $entityManager = $this->getEntityManager();
        
        $serviceManager = new ServiceManager();
        $serviceManager->setService('entity-manager', $entityManager);
        $serviceManager->setInvokableClass('object-manager-aware', 'DoctrineORMModuleTest\Service\ObjectManagerAwareDummy');
        $serviceManager->addInitializer(new EntityManagerInitializer('entity-manager'));
    
        $this->assertSame($entityManager, $serviceManager->get('object-manager-aware')->getObjectManager());
    }
}
