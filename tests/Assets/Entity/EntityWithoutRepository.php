<?php

declare(strict_types=1);

namespace DoctrineORMModuleTest\Assets\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class EntityWithoutRepository
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private int|null $id = null;

    public function getId(): int|null
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }
}
