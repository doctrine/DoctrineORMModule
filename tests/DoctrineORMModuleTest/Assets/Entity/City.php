<?php

declare(strict_types=1);

namespace DoctrineORMModuleTest\Assets\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="doctrine_orm_module_city")
 */
class City
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected int $id;

    /** @ORM\Column(type="string", nullable=true) */
    protected string $name;

    /** @ORM\OneToOne(targetEntity="Country") */
    protected Country $country;

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

    public function setCountry(Country $country) : self
    {
        $this->country = $country;

        return $this;
    }

    public function getCountry() : string
    {
        return $this->country;
    }
}
