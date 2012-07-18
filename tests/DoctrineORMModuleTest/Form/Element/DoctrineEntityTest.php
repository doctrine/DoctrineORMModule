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

namespace DoctrineORMModuleTest\Form\Element;

use DoctrineORMModuleTest\Framework\TestCase;
use DoctrineORMModuleTest\Assets\Fixture\TestFixture;

use Doctrine\Common\DataFixtures\Loader as FixtureLoader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use DoctrineORMModule\Form\Element\DoctrineEntity as DoctrineEntityElement;


class DoctrineElementTest extends TestCase
{
    /**
     * @var QueryBuilder
     */
    protected $qb;

    /**
     * @var DoctrineEntityElement
     */
    protected $element;

    public function setUp()
    {
        parent::setUp();

        $this->createDb();
        $loader = new FixtureLoader();
        $loader->addFixture(new TestFixture());
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->getEntityManager(), $purger);
        $executor->execute($loader->getFixtures());

        $this->element = new DoctrineEntityElement('foo', array(
            'object_manager' => $this->getEntityManager(),
            'target_class' => 'DoctrineORMModuleTest\Assets\Entity\Test'
        ));
    }

    public function testCanGetEntitiesWithDefaultSettings()
    {
        $entities = $this->element->getEntities();
        $this->assertEquals(100, count($entities));
    }

    public function testCanGetEntitiesWithSpec()
    {
        $this->element->setSpec(function($repository) {
            return $repository->findById(1);
        });
        $entities = $this->element->getEntities();

        $this->assertEquals(1, count($entities));
    }

    public function testCanGenerateCorrectOptionsForForm()
    {
        $entities = $this->element->getEntities();
        $attributes = $this->element->getAttributes();
        $options = $attributes['options'];

        $firstEntity = $entities[0];
        $firstOption = $options[0];

        $this->assertEquals($firstEntity->__toString(), $firstOption['label']);
        $this->assertEquals($firstEntity->getId(), $firstOption['value']);
    }

    public function testCanGenerateCorrectOptionsForFormWithProperty()
    {
        $this->element->setProperty('password');
        $entities = $this->element->getEntities();
        $attributes = $this->element->getAttributes();
        $options = $attributes['options'];

        $firstEntity = $entities[0];
        $firstOption = $options[0];

        $this->assertEquals($firstEntity->getPassword(), $firstOption['label']);
        $this->assertEquals($firstEntity->getId(), $firstOption['value']);
    }

    public function testProvidesInputSpecificationThatIncludesValidatorsBasedOnAttributes()
    {
        $element = new DoctrineEntityElement('foo');
        $element->setOptions(array(
            'object_manager' => $this->getEntityManager(),
            'target_class' => 'DoctrineORMModuleTest\Assets\Entity\Test'
        ));

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);

        $expectedClasses = array(
            'DoctrineModule\Validator\ObjectExists'
        );
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertTrue(in_array($class, $expectedClasses), $class);
        }
    }
}