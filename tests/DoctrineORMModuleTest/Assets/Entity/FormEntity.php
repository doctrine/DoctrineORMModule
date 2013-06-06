<?php

namespace DoctrineORMModuleTest\Assets\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation as Form;

/**
 * @ORM\Entity
 * @ORM\Table(name="doctrine_orm_module_form_entity")
 */
class FormEntity
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $checkbox;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @Form\Options({"property":"name"})
     * @ORM\OneToOne(targetEntity="TargetInterface")
     */
    protected $targetOne;

    /**
     * @Form\Options({"property":"name"})
     * @ORM\OneToOne(targetEntity="TargetInterface")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $targetOneNullable;

    /**
     * @Form\Options({"property":"name"})
     * @ORM\OneToMany(targetEntity="FormEntityTarget", mappedBy="formEntity")
     */
    protected $targetMany;

    /**
     * @param  boolean    $checkbox
     * @return FormEntity
     */
    public function setCheckbox($checkbox)
    {
        $this->checkbox = $checkbox;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getCheckbox()
    {
        return $this->checkbox;
    }

    /**
     * @param  int        $id
     * @return FormEntity
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param  string     $name
     * @return FormEntity
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
