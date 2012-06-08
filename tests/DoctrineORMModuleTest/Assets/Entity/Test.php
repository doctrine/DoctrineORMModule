<?php
namespace DoctrineORMModuleTest\Assets\Entity;

/**
 * @Entity
 * @Table(name="doctrine_orm_module_test")
 */
class Test
{
    /**
     * @Id
     * @Column(type="integer");
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Column(type="string")
     */
    protected $username;

    /**
     * @Column(type="string")
     */
    protected $password;

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = (string) $password;
    }

    /**
     * @return string|null
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = (string) $username;
    }

    /**
     * @return string|null
     */
    public function getUsername()
    {
        return $this->username;
    }
}
