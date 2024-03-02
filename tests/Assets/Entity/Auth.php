<?php

declare(strict_types=1);

namespace DoctrineORMModuleTest\Assets\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'doctrine_orm_module_auth')]
class Auth
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ORM\Column(type: 'string', nullable: true)]
    private string $username;

    #[ORM\Column(type: 'string', nullable: true)]
    private string $password;

    public function getId(): int|null
    {
        return $this->id;
    }

    public function setPassword(string $password): void
    {
        $this->password = (string) $password;
    }

    public function getPassword(): string|null
    {
        return $this->password;
    }

    public function setUsername(string $username): void
    {
        $this->username = (string) $username;
    }

    public function getUsername(): string|null
    {
        return $this->username;
    }

    /**
     * Used for testing DoctrineEntity form element
     */
    public function __toString(): string
    {
        return (string) $this->username;
    }
}
