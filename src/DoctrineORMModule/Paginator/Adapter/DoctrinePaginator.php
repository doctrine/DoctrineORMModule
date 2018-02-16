<?php

namespace DoctrineORMModule\Paginator\Adapter;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Zend\Paginator\Adapter\AdapterInterface;

/**
 * Paginator adapter for the Zend\Paginator component
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @since   0.1.0
 * @author  Tõnis Tobre <tobre@bitweb.ee>
 */
class DoctrinePaginator implements AdapterInterface
{
    /**
     * @var Paginator
     */
    protected $paginator;

    /**
     * Constructor
     *
     * @param Paginator $paginator
     */
    public function __construct(Paginator $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * @param  Paginator $paginator
     * @return self
     */
    public function setPaginator(Paginator $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * @return Paginator
     */
    public function getPaginator()
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
