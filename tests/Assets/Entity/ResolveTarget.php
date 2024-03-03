<?php

declare(strict_types=1);

namespace DoctrineORMModuleTest\Assets\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ResolveTarget
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected int $id;

    #[ORM\ManyToOne(targetEntity: Target::class)]
    #[ORM\JoinColumn(name: 'target_id', referencedColumnName: 'id', unique: true)]
    protected Target $target;
}
