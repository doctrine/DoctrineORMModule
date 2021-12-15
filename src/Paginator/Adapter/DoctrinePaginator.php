<?php

declare(strict_types=1);

namespace DoctrineORMModule\Paginator\Adapter;

use Doctrine\ORM\Tools\Pagination\Paginator;
use JsonSerializable;
use Laminas\Paginator\Adapter\AdapterInterface;
use ReturnTypeWillChange;

/**
 * Paginator adapter for the Laminas\Paginator component
 */
class DoctrinePaginator implements AdapterInterface, JsonSerializable
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

    public function setPaginator(Paginator $paginator): self
    {
        $this->paginator = $paginator;

        return $this;
    }

    public function getPaginator(): Paginator
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
    #[ReturnTypeWillChange]
    public function count()
    {
        return $this->paginator->count();
    }

    /**
     * @return array{select: string, count_select: int}
     */
    public function jsonSerialize(): array
    {
        return [
            'select' => $this->paginator->getQuery()->getSQL(),
            'count_select' => $this->paginator->count(),
        ];
    }
}
