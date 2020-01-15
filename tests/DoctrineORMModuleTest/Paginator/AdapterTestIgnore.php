<?php

declare(strict_types=1);

namespace DoctrineORMModuleTest\Paginator;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader as FixtureLoader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use DoctrineORMModuleTest\Assets\Entity\Test;
use DoctrineORMModuleTest\Assets\Fixture\TestFixture;
use DoctrineORMModuleTest\Framework\TestCase;
use function count;

class AdapterTestIgnore extends TestCase
{
    protected QueryBuilder $qb;

    protected PaginatorAdapter $paginatorAdapter;

    protected DoctrinePaginator $paginator;

    public function setUp() : void
    {
        parent::setUp();

        $this->createDb();
        $loader = new FixtureLoader();
        $loader->addFixture(new TestFixture());
        $purger   = new ORMPurger();
        $executor = new ORMExecutor($this->getEntityManager(), $purger);
        $executor->execute($loader->getFixtures());

        $this->qb = $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('t')
            ->from(Test::class, 't')
            ->orderBy('t.id', 'ASC');

        $this->paginator        = new DoctrinePaginator($this->qb);
        $this->paginatorAdapter = new PaginatorAdapter($this->paginator);
    }

    public function testCanSetPaginator() : void
    {
        $this->assertSame($this->paginator, $this->paginatorAdapter->getPaginator());
        $doctrinePaginator = new DoctrinePaginator($this->getEntityManager()->createQuery(''));
        $this->paginatorAdapter->setPaginator($doctrinePaginator);
        $this->assertSame($doctrinePaginator, $this->paginatorAdapter->getPaginator());
    }

    public function testContainedItemsCount() : void
    {
        $itemsCount = $this->qb->select('COUNT(t)')->getQuery()->getSingleScalarResult();

        $this->assertEquals($itemsCount, $this->paginatorAdapter->count());
        $this->assertEquals($itemsCount, count($this->paginatorAdapter->getItems(0, $itemsCount + 100)));
    }

    public function testGetsItemsAtOffsetZero() : void
    {
        $expected = $this->qb->setMaxResults(10)->getQuery()->getResult();
        $actual   = $this->paginatorAdapter->getItems(0, 10);

        foreach ($expected as $key => $expectedItem) {
            $this->assertEquals($expectedItem, $actual[$key]);
        }
    }

    public function testGetsItemsAtOffsetTen() : void
    {
        $expected = $this->qb->setFirstResult(10)->setMaxResults(10)->getQuery()->getResult();
        $actual   = $this->paginatorAdapter->getItems(10, 10);

        foreach ($expected as $key => $expectedItem) {
            $this->assertEquals($expectedItem, $actual[$key]);
        }
    }
}
