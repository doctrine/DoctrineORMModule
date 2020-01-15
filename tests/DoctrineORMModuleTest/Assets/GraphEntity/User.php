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
class User
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected int $id;

    /**
     * @ORM\ManyToMany(targetEntity="UserGroup", mappedBy="users")
     *
     * @var Collection|UserGroup[]
     */
    protected Collection $groups;

    /**
     * @ORM\OneToMany(targetEntity="Session", mappedBy="user")
     *
     * @var Collection|Session[]
     */
    protected Collection $sessions;

    /** @ORM\OneToOne(targetEntity="Address") */
    protected Address $address;

    public function __construct()
    {
        $this->groups   = new ArrayCollection();
        $this->sessions = new ArrayCollection();
    }
}
