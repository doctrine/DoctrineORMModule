<?php

declare(strict_types=1);

namespace DoctrineORMModuleTest\Assets\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Laminas\Form\Annotation as Form;

/**
 * @ORM\Entity
 * @ORM\Table(name="doctrine_orm_module_form_entity")
 */
class FormEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    protected $bool;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    protected $boolean;

    /**
     * @ORM\Column(type="float")
     *
     * @var float
     */
    protected $float;

    /**
     * @ORM\Column(type="bigint")
     *
     * @var int
     */
    protected $bigint;

    /**
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $integer;

    /**
     * @ORM\Column(type="smallint")
     *
     * @var int
     */
    protected $smallint;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var DateTime
     */
    protected $datetime;

    /**
     * @ORM\Column(type="datetimetz")
     *
     * @var DateTime
     */
    protected $datetimetz;

    /**
     * @ORM\Column(type="date")
     *
     * @var DateTime
     */
    protected $date;

    /**
     * @ORM\Column(type="time")
     *
     * @var DateTime
     */
    protected $time;

    /**
     * @ORM\Column(type="text")
     *
     * @var string
     */
    protected $text;

    /**
     * @ORM\Column(type="string", nullable=false, length=20)
     *
     * @var string
     */
    protected $string;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    protected $stringNullable;

    /**
     * @ORM\OneToOne(targetEntity="Target")
     *
     * @var Target
     */
    protected $targetOne;

    /**
     * @ORM\OneToOne(targetEntity="Target")
     * @ORM\JoinColumn(nullable=true)
     *
     * @var Target|null
     */
    protected $targetOneNullable;

   /**
    * @ORM\OneToOne(targetEntity="Target")
    * @ORM\JoinColumn(nullable=true)
    *
    * @Form\Type("DoctrineModule\Form\Element\ObjectSelect")
    * @Form\Options({"empty_option":null})
    *
    * @var Target|null
    */
    protected $noDisplayEmptyOption;

    /**
     * @ORM\OneToMany(targetEntity="FormEntityTarget", mappedBy="formEntity")
     *
     * @var Target[]
     */
    protected $targetMany;

    /**
     * @ORM\Column(type="integer")
     *
     * @Form\Options({"label":"Please Choose", "value_options":{"f":"false","t":"true"}})
     * @Form\Type("Radio")
     *
     * @var int
     */
    protected $specificType;

    /**
     * @ORM\OneToMany(targetEntity="FormEntityTarget", mappedBy="formEntityMulti")
     *
     * @Form\Type("DoctrineORMModule\Form\Element\EntityMultiCheckbox")
     *
     * @var FormEntityTarget[]
     */
    protected $specificMultiType;

    /**
     * @ORM\Column(type="integer")
     *
     * @Form\Options({"label":"Please Choose", "value_options":{"f":"false","t":"true"}})
     * @Form\Attributes({"type":"textarea"})
     *
     * @var int
     */
    protected $specificAttributeType;

    /**
     * @ORM\Column(type="string", length=256)
     * @ORM\JoinColumn(nullable=true)
     *
     * @Form\Type("File")
     * @Form\Options({"label":"Image"})
     *
     * @var string
     */
    protected $image;
}
