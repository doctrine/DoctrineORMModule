<?php

namespace DoctrineORMModuleTest\Assets\GraphEntity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Part of the test assets used to produce a demo of graphs in the Laminas Developer Tools integration
 *
 * @link    http://www.doctrine-project.org/
 *
 * @ORM\Entity()
 */
class Session
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="address")
     *
     * @var User
     */
    protected $user;
}
