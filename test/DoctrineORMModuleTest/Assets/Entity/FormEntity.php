<?php

namespace DoctrineORMModuleTest\Assets\Entity;

use Doctrine\ORM\Mapping as ORM;
use Laminas\Form\Annotation as Form;

/**
 * @ORM\Entity
 * @ORM\Table(name="doctrine_orm_module_form_entity")
 *
 * @author Kyle Spraggs <theman@spiffyjr.me>
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
    * @Form\Type("DoctrineModule\Form\Element\ObjectSelect")
    * @ORM\OneToOne(targetEntity="TargetInterface")
    * @ORM\JoinColumn(nullable=true)
    * @Form\Options({"empty_option":null})
    */
    protected $noDisplayEmptyOption;

    /**
     * @ORM\OneToMany(targetEntity="FormEntityTarget", mappedBy="formEntity")
     */
    protected $targetMany;

    /**
     * @ORM\Column(type="integer")
     * @Form\Options({"label":"Please Choose", "value_options":{"f":"false","t":"true"}})
     * @Form\Type("Radio")
     */
    protected $specificType;

    /**
     * @ORM\OneToMany(targetEntity="FormEntityTarget", mappedBy="formEntityMulti")
     * @Form\Type("DoctrineORMModule\Form\Element\EntityMultiCheckbox")
     */
    protected $specificMultiType;

    /**
     * @ORM\Column(type="integer")
     * @Form\Options({"label":"Please Choose", "value_options":{"f":"false","t":"true"}})
     * @Form\Attributes({"type":"textarea"})
     */
    protected $specificAttributeType;

    /**
     * @ORM\Column(type="string", length=256)
     * @Form\Type("File")
     * @ORM\JoinColumn(nullable=true)
     * @Form\Options({"label":"Image"})
     */
    protected $image;
}
