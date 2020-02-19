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
class UserGroup
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var \Doctrine\Common\Collections\Collection|User[]
     * @ORM\ManyToMany(targetEntity="User", inversedBy="groups")
     */
    protected $users;

    /* */
    public function __construct()
    {
        $this->users = new ArrayCollection();
    }
}
