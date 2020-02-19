<?php

namespace DoctrineORMModuleTest\Assets\GraphEntity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Part of the test assets used to produce a demo of graphs in the Laminas Developer Tools integration
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Marco Pivetta <ocramius@gmail.com>
 *
 * @ORM\Entity()
 */
class User
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var \Doctrine\Common\Collections\Collection|UserGroup[]
     * @ORM\ManyToMany(targetEntity="UserGroup", mappedBy="users")
     */
    protected $groups;

    /**
     * @var \Doctrine\Common\Collections\Collection|Session[]
     * @ORM\OneToMany(targetEntity="Session", mappedBy="user")
     */
    protected $sessions;

    /**
     * @var Address
     * @ORM\OneToOne(targetEntity="Address")
     */
    protected $address;

    /**
     *
     */
    public function __construct()
    {
        $this->groups   = new ArrayCollection();
        $this->sessions = new ArrayCollection();
    }
}
