<?php

declare(strict_types=1);

namespace DoctrineORMModuleTest\Assets\GraphEntity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Part of the test assets used to produce a demo of graphs in the Laminas Developer Tools integration
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
     *
     * @var int
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="UserGroup", mappedBy="users")
     *
     * @var Collection|UserGroup[]
     */
    protected $groups;

    /**
     * @ORM\OneToMany(targetEntity="Session", mappedBy="user")
     *
     * @var Collection|Session[]
     */
    protected $sessions;

    /**
     * @ORM\OneToOne(targetEntity="Address")
     *
     * @var Address
     */
    protected $address;

    public function __construct()
    {
        $this->groups   = new ArrayCollection();
        $this->sessions = new ArrayCollection();
    }
}
