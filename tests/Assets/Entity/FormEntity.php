<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace DoctrineORMModuleTest\Assets\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation as Form;

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
