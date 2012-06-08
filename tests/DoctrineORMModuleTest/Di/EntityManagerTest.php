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
use Doctrine\ORM\EntityManager;

class EntityManagerTest extends TestCase
{
    /**
     * Verifying that the EntityManager that is injected into a custom object is actually the same that can be
     * retrieved directly or from the locator
     */
    public function testCanInjectEntityManager()
    {
        $locator = $this->getServiceManager();
        /* @var $target EntityManagerTestInjectTarget */
        $target = $locator->get('DoctrineORMModuleTest\Di\\EntityManagerTestInjectTarget');
        $this->assertInstanceOf('DoctrineORMModuleTest\Di\\EntityManagerTestInjectTarget', $target);
        $em = $target->getEntityManager();
        $this->assertInstanceOf('Doctrine\ORM\EntityManager', $em);
        $this->assertSame($em, $locator->get('Doctrine\ORM\EntityManager'));
    }
}

class EntityManagerTestInjectTarget
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->em;
    }
}
