<?php

declare(strict_types=1);

namespace DoctrineORMModuleTest\Assets\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'doctrine_orm_module_product')]
class Product
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected int $id;

    #[ORM\Column(type: 'string', nullable: true)]
    protected string $name;

    /** @var Category[] */
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'products')]
    #[ORM\JoinTable(name: 'ProductCategory')]
    #[ORM\JoinColumn(name: 'Product_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'Category_id', referencedColumnName: 'id')]
    private ArrayCollection $categories;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    public function getId(): int|null
    {
        return $this->id;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /** @param Category[] $categories */
    public function setCategories(ArrayCollection $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    /** @return Category[] */
    public function getCategories(): ArrayCollection
    {
        return $this->categories;
    }
}
