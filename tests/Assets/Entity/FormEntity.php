<?php

declare(strict_types=1);

namespace DoctrineORMModuleTest\Assets\Entity;

use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Laminas\Form\Annotation as Form;

#[ORM\Entity]
#[ORM\Table(name: 'doctrine_orm_module_form_entity')]
class FormEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    protected int $id;

    #[ORM\Column(type: 'boolean', nullable: false)]
    protected bool $bool;

    #[ORM\Column(type: 'boolean', nullable: false)]
    protected bool $boolean;

    #[ORM\Column(type: 'float', nullable: false)]
    protected float $float;

    #[ORM\Column(type: 'bigint', nullable: false)]
    protected string $bigint;

    #[ORM\Column(type: 'integer', nullable: false)]
    protected int $integer;

    #[ORM\Column(type: 'smallint', nullable: false)]
    protected int $smallint;

    #[ORM\Column(type: 'datetime', nullable: false)]
    protected DateTime $datetime;

    #[ORM\Column(type: 'datetime_immutable', nullable: false)]
    protected DateTimeImmutable $datetimeImmutable;

    #[ORM\Column(type: 'datetimetz', nullable: false)]
    protected DateTime $datetimetz;

    #[ORM\Column(type: 'datetimetz_immutable', nullable: false)]
    protected DateTimeImmutable $datetimetzImmutable;

    #[ORM\Column(type: 'date', nullable: false)]
    protected DateTime $date;

    #[ORM\Column(type: 'time', nullable: false)]
    protected DateTime $time;

    #[ORM\Column(type: 'text', nullable: false)]
    protected string $text;

    #[ORM\Column(type: 'string', length: 20, nullable: false)]
    protected string $string;

    #[ORM\Column(type: 'string', nullable: true)]
    protected string|null $stringNullable = null;

    /**
     * This join is odd because the targetEntity exists only as an interface
     **/
    #[ORM\OneToOne(targetEntity: Target::class)]
    protected Target $targetOne;

    /**
     * This join is odd because the targetEntity exists only as an interface
     */
    #[ORM\OneToOne(targetEntity: Target::class)]
    #[ORM\JoinColumn(nullable: true)]
    protected Target|null $targetOneNullable = null;

    /**
     * @Form\Type("File")
     * @Form\Options({"label":"Image"})
     */
    #[ORM\Column(type: 'string', length: 256, nullable: true)]
    #[ORM\JoinColumn(nullable: true)]
    protected string $image;

    /**
     * @Form\Options({"label":"Please Choose", "value_options":{"f":"false","t":"true"}})
     * @Form\Type("Radio")
     */
    #[ORM\Column(type: 'integer')]
    protected int $specificType;

    /**
     * @Form\Options({"label":"Please Choose", "value_options":{"f":"false","t":"true"}})
     * @Form\Attributes({"type":"textarea"})
     */
    #[ORM\Column(type: 'integer')]
    protected int $specificAttributeType;

    /**
     * @Form\Type("DoctrineORMModule\Form\Element\EntityMultiCheckbox")
     *
     * @var FormEntityTarget[]
     */
    #[ORM\OneToMany(targetEntity: FormEntityTarget::class, mappedBy: 'formEntityMulti')]
    protected array $specificMultiType;

    /** @var Target[] */
    #[ORM\OneToMany(targetEntity: FormEntityTarget::class, mappedBy: 'formEntity')]
    protected array $targetMany;

    /**
     * This join is odd because the targetEntity exists only as an interface
     *
     * @Form\Type("DoctrineModule\Form\Element\ObjectSelect")
     * @Form\Options({"empty_option":null})
     */
    #[ORM\OneToOne(targetEntity: Target::class)]
    #[ORM\JoinColumn(nullable: true)]
    protected Target|null $noDisplayEmptyOption = null;
}
