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

namespace DoctrineORMModuleTest\Paginator;

use DoctrineORMModuleTest\Framework\TestCase;
use DoctrineORMModuleTest\Assets\Fixture\TestFixture;

use Doctrine\Common\DataFixtures\Loader as FixtureLoader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;

class AdapterTest extends TestCase
{
    /**
     * @var QueryBuilder
     */
    protected $qb;

    /**
     * @var PaginatorAdapter
     */
    protected $paginatorAdapter;

    /**
     * @var DoctrinePaginator
     */
    protected $paginator;

    public function setUp()
    {
        parent::setUp();

        $this->createDb();
        $loader = new FixtureLoader();
        $loader->addFixture(new TestFixture());
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->getEntityManager(), $purger);
        $executor->execute($loader->getFixtures());

        $this->qb = $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('t')
            ->from('DoctrineORMModuleTest\Assets\Entity\Test', 't')
            ->orderBy('t.id', 'ASC');

        $this->paginator = new DoctrinePaginator($this->qb);
        $this->paginatorAdapter = new PaginatorAdapter($this->paginator);
    }

    public function testCanSetPaginator()
    {
        $this->assertSame($this->paginator, $this->paginatorAdapter->getPaginator());
        $doctrinePaginator = new DoctrinePaginator($this->getEntityManager()->createQuery(''));
        $this->paginatorAdapter->setPaginator($doctrinePaginator);
        $this->assertSame($doctrinePaginator, $this->paginatorAdapter->getPaginator());
    }

    public function testContainedItemsCount()
    {
        $itemsCount = $this->qb->select('COUNT(t)')->getQuery()->getSingleScalarResult();

        $this->assertEquals($itemsCount, $this->paginatorAdapter->count());
        $this->assertEquals($itemsCount, count($this->paginatorAdapter->getItems(0, $itemsCount + 100)));

    }

    public function testGetsItemsAtOffsetZero()
    {
        $expected = $this->qb->setMaxResults(10)->getQuery()->getResult();
        $actual = $this->paginatorAdapter->getItems(0, 10);

        foreach ($expected as $key => $expectedItem) {
            $this->assertEquals($expectedItem, $actual[$key]);
        }
    }

    public function testGetsItemsAtOffsetTen()
    {
        $expected = $this->qb->setFirstResult(10)->setMaxResults(10)->getQuery()->getResult();
        $actual = $this->paginatorAdapter->getItems(10, 10);

        foreach ($expected as $key => $expectedItem) {
            $this->assertEquals($expectedItem, $actual[$key]);
        }
    }
}
