<?php

declare(strict_types=1);

namespace DoctrineORMModuleTest\Assets\Entity;

use Doctrine\ORM\Mapping as ORM;
use DoctrineORMModuleTest\Assets\Entity\Target as TargetInterface;

/**
 * @ORM\Entity
 */
class TargetEntity implements TargetInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;
}
