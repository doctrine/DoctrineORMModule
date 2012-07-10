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

namespace DoctrineORMModuleTest\Hydrator;

use DoctrineORMModuleTest\Framework\TestCase;
use DoctrineORMModuleTest\Assets\Fixture\TestFixture;
use DoctrineORMModuleTest\Assets\Entity\Test as SimpleEntity;
use DoctrineORMModuleTest\Assets\Entity\Product as ProductEntity;
use DoctrineORMModuleTest\Assets\Entity\City as CityEntity;

use Doctrine\Common\DataFixtures\Loader as FixtureLoader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

use DoctrineORMModule\Hydrator\DoctrineEntity as DoctrineEntityHydrator;


class DoctrineEntityTest extends TestCase
{
    protected $hydrator;

    public function setUp()
    {
        parent::setUp();

        $this->createDb();
        $loader = new FixtureLoader();
        $loader->addFixture(new TestFixture());
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->getEntityManager(), $purger);
        $executor->execute($loader->getFixtures());

        $this->hydrator = new DoctrineEntityHydrator($this->getEntityManager());
    }

    public function testCanHydrateSimpleEntity()
    {
        $data = array(
            'username' => 'foo',
            'password' => 'bar'
        );

        $entity = $this->hydrator->hydrate($data, new SimpleEntity());
        $extract = $this->hydrator->extract($entity);

        $this->assertEquals($data, $extract);
    }

    public function testCanHydrateOneToOneEntity()
    {
        $data = array(
            'name' => 'Paris',
            'country' => 1
        );

        $entity = $this->hydrator->hydrate($data, new CityEntity());
        $this->assertInstanceOf('DoctrineORMModuleTest\Assets\Entity\Country', $entity->getCountry());
    }

    public function testCanHydrateOneToManyEntity()
    {
        $data = array(
            'name' => 'Chair',
            'categories' => array(
                1, 2, 3
            )
        );

        $entity = $this->hydrator->hydrate($data, new ProductEntity());
        $this->assertEquals(3, count($entity->getCategories()));

        foreach ($entity->getCategories() as $category) {
            $this->assertInstanceOf('DoctrineORMModuleTest\Assets\Entity\Category', $category);
        }
    }
}
