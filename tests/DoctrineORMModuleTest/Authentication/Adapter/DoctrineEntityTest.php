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

namespace DoctrineORMModuleTest\Authentication\Adapter;

use DoctrineORMModuleTest\Framework\TestCase;

class DoctrineEntityTest extends TestCase
{
    public function setUp()
    {
        $this->createDb();
    }

    public function testInvalidLogin()
    {
        $em = $this->getEntityManager();

        $adapter = new \DoctrineModule\Authentication\Adapter\DoctrineEntity(
            $em,
            'DoctrineORMModuleTest\Assets\Entity\Test'
        );
        $adapter->setIdentity('username');
        $adapter->setCredential('password');

        $result = $adapter->authenticate();

        $this->assertFalse($result->isValid());
    }

    public function testValidLogin()
    {
        $em = $this->getEntityManager();

        $entity = new \DoctrineORMModuleTest\Assets\Entity\Test;
        $entity->setUsername('username');
        $entity->setPassword('password');
        $em->persist($entity);
        $em->flush();

        $adapter = new \DoctrineModule\Authentication\Adapter\DoctrineEntity(
            $em,
            'DoctrineORMModuleTest\Assets\Entity\Test'
        );
        $adapter->setIdentity('username');
        $adapter->setCredential('password');

        $result = $adapter->authenticate();

        $this->assertTrue($result->isValid());
    }

    public function tearDown()
    {
        $this->dropDb();
    }
}
