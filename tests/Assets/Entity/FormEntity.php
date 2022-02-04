<?php

declare(strict_types=1);

namespace DoctrineORMModuleTest\Assets\Entity;

use DateTime;
use DateTimeImmutable;
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
     */
    protected int $id;

    /** @ORM\Column(type="boolean") */
    protected bool $bool;

    /** @ORM\Column(type="boolean") */
    protected bool $boolean;

    /** @ORM\Column(type="float") */
    protected float $float;

    /** @ORM\Column(type="bigint") */
    protected int $bigint;

    /** @ORM\Column(type="integer") */
    protected int $integer;

    /** @ORM\Column(type="smallint") */
    protected int $smallint;

    /** @ORM\Column(type="datetime") */
    protected DateTime $datetime;

    /** @ORM\Column(type="datetime_immutable") */
    protected DateTime $datetimeImmutable;

    /** @ORM\Column(type="datetimetz") */
    protected DateTimeImmutable $datetimetz;

    /** @ORM\Column(type="datetimetz_immutable") */
    protected DateTimeImmutable $datetimetzImmutable;

    /** @ORM\Column(type="date") */
    protected DateTime $date;

    /** @ORM\Column(type="time") */
    protected DateTime $time;

    /** @ORM\Column(type="text") */
    protected string $text;

    /** @ORM\Column(type="string", nullable=false, length=20) */
    protected string $string;

    /** @ORM\Column(type="string", nullable=true) */
    protected ?string $stringNullable = null;

    /** @ORM\OneToOne(targetEntity="Target") */
    protected Target $targetOne;

    /**
     * @ORM\OneToOne(targetEntity="Target")
     * @ORM\JoinColumn(nullable=true)
     */
    protected ?Target $targetOneNullable = null;

   /**
    * @ORM\OneToOne(targetEntity="Target")
    * @ORM\JoinColumn(nullable=true)
    *
    * @Form\Type("DoctrineModule\Form\Element\ObjectSelect")
    * @Form\Options({"empty_option":null})
    */
    protected ?Target $noDisplayEmptyOption = null;

    /**
     * @ORM\OneToMany(targetEntity="FormEntityTarget", mappedBy="formEntity")
     *
     * @var Target[]
     */
    protected array $targetMany;

    /**
     * @ORM\Column(type="integer")
     *
     * @Form\Options({"label":"Please Choose", "value_options":{"f":"false","t":"true"}})
     * @Form\Type("Radio")
     */
    protected int $specificType;

    /**
     * @ORM\OneToMany(targetEntity="FormEntityTarget", mappedBy="formEntityMulti")
     *
     * @Form\Type("DoctrineORMModule\Form\Element\EntityMultiCheckbox")
     *
     * @var FormEntityTarget[]
     */
    protected array $specificMultiType;

    /**
     * @ORM\Column(type="integer")
     *
     * @Form\Options({"label":"Please Choose", "value_options":{"f":"false","t":"true"}})
     * @Form\Attributes({"type":"textarea"})
     */
    protected int $specificAttributeType;

    /**
     * @ORM\Column(type="string", length=256)
     * @ORM\JoinColumn(nullable=true)
     *
     * @Form\Type("File")
     * @Form\Options({"label":"Image"})
     */
    protected string $image;
}
