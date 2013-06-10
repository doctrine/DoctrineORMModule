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
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="bool")
     */
    protected $bool;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $boolean;

    /**
     * @ORM\Column(type="float")
     */
    protected $float;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $bigint;

    /**
     * @ORM\Column(type="integer")
     */
    protected $integer;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $smallint;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $datetime;

    /**
     * @ORM\Column(type="datetimetz")
     */
    protected $datetimetz;

    /**
     * @ORM\Column(type="date")
     */
    protected $date;

    /**
     * @ORM\Column(type="time")
     */
    protected $time;

    /**
     * @ORM\Column(type="text")
     */
    protected $text;

    /**
     * @ORM\Column(type="string", nullable=false, length=20)
     */
    protected $string;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $stringNullable;

    /**
     * @ORM\OneToOne(targetEntity="TargetInterface")
     */
    protected $targetOne;

    /**
     * @ORM\OneToOne(targetEntity="TargetInterface")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $targetOneNullable;

    /**
     * @ORM\OneToMany(targetEntity="FormEntityTarget", mappedBy="formEntity")
     */
    protected $targetMany;
}
