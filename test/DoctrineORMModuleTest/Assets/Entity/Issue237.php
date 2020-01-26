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
class Issue237
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
}
