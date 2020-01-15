<?php

declare(strict_types=1);

namespace DoctrineORMModuleTest\Assets\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="doctrine_orm_module_product")
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected int $id;

    /** @ORM\Column(type="string", nullable=true) */
    protected string $name;

    /**
     * @ORM\ManyToMany(targetEntity="Category")
     *
     * @var ?array
     */
    protected $categories;

    public function getId() : ?int
    {
        return $this->id;
    }

    public function setName(string $name) : self
    {
        $this->name = $name;

        return $this;
    }

    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param Category[] $categories
     */
    public function setCategories(array $categories) : self
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * @return Category[]
     */
    public function getCategories() : array
    {
        return $this->categories;
    }
}
