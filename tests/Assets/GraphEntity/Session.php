<?php

declare(strict_types=1);

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
     */
    protected int $id;

    /** @ORM\ManyToOne(targetEntity="User", inversedBy="address") */
    protected User $user;
}
