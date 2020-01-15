<?php

declare(strict_types=1);

namespace DoctrineORMModuleTest\Assets\GraphEntity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Part of the test assets used to produce a demo of graphs in the ZDT integration
 *
 * @link    http://www.doctrine-project.org/
 *
 * @ORM\Entity()
 */
class UserGroup
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected int $id;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="groups")
     *
     * @var Collection|User[]
     */
    protected Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }
}
