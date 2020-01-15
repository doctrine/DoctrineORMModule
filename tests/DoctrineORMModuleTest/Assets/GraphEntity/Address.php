<?php

declare(strict_types=1);

namespace DoctrineORMModuleTest\Assets\GraphEntity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Part of the test assets used to produce a demo of graphs in the ZDT integration
 *
 * @link    http://www.doctrine-project.org/
 *
 * @ORM\Entity()
 */
class Address
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected int $id;

    public function __construct()
    {
        $this->groups = new ArrayCollection();
    }
}
