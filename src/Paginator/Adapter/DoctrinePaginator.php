<?php

declare(strict_types=1);

namespace DoctrineORMModule\Paginator\Adapter;

use ArrayIterator;
use Doctrine\ORM\Tools\Pagination\Paginator;
use JsonSerializable;
use Laminas\Paginator\Adapter\AdapterInterface;

/**
 * Paginator adapter for the Laminas\Paginator component
 *
 * @psalm-template T of object
 * @psalm-template TKey of int
 * @psalm-template TValue
 * @template-implements AdapterInterface<TKey, TValue>
 */
class DoctrinePaginator implements AdapterInterface, JsonSerializable
{
    /** @var Paginator<T> */
    protected Paginator $paginator;

    /**
     * Constructor
     *
     * @param Paginator<T> $paginator
     */
    public function __construct(Paginator $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * @param Paginator<T> $paginator
     */
    public function setPaginator(Paginator $paginator): self
    {
        $this->paginator = $paginator;

        return $this;
    }

    /**
     * @return Paginator<T>
     */
    public function getPaginator(): Paginator
    {
        return $this->paginator;
    }

    /**
     * {@inheritDoc}
     *
     * @param int|null $offset
     * @param int|null $itemCountPerPage
     *
     * @return ArrayIterator<TKey, TValue>
     *
     * @psalm-suppress LessSpecificImplementedReturnType
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $this->paginator
             ->getQuery()
             ->setFirstResult($offset)
             ->setMaxResults($itemCountPerPage);

        return $this->paginator->getIterator();
    }

    public function count(): int
    {
        return $this->paginator->count();
    }

    /**
     * @return array{select: list<string>|string, count_select: int}
     */
    public function jsonSerialize(): array
    {
        return [
            'select' => $this->paginator->getQuery()->getSQL(),
            'count_select' => $this->paginator->count(),
        ];
    }
}
