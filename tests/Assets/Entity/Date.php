<?php

declare(strict_types=1);

namespace DoctrineORMModuleTest\Assets\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'doctrine_orm_module_date')]
class Date
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ORM\Column(type: 'date', nullable: true)]
    private DateTime $date;

    public function getId(): int|null
    {
        return $this->id;
    }

    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }
}
