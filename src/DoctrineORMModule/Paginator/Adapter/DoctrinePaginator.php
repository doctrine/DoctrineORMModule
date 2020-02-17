<?php

declare(strict_types=1);

namespace DoctrineORMModule\Paginator\Adapter;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Laminas\Paginator\Adapter\AdapterInterface;

/**
 * Paginator adapter for the Laminas\Paginator component
 */
class DoctrinePaginator implements AdapterInterface
{
    /** @var Paginator */
    protected $paginator;

    /**
     * Constructor
     */
    public function __construct(Paginator $paginator)
    {
        $this->paginator = $paginator;
    }

    public function setPaginator(Paginator $paginator) : self
    {
        $this->paginator = $paginator;
    }

    public function getPaginator() : Paginator
    {
        return $this->paginator;
    }

    /**
     * {@inheritDoc}
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $this->paginator
             ->getQuery()
             ->setFirstResult($offset)
             ->setMaxResults($itemCountPerPage);

        return $this->paginator->getIterator();
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return $this->paginator->count();
    }
}
