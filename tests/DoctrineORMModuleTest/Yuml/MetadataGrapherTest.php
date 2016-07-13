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

namespace DoctrineORMModuleTest\Yuml;

use DoctrineORMModule\Yuml\MetadataGrapher;
use PHPUnit_Framework_TestCase;

/**
 * Tests for the metadata to string converter
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Marco Pivetta <ocramius@gmail.com>
 */
class MetadataGrapherTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var MetadataGrapher
     */
    protected $grapher;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->grapher = new MetadataGrapher();
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawSimpleEntity()
    {
        $class = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class->expects($this->any())->method('getName')->will($this->returnValue('Simple\\Entity'));
        $class->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));
        $class->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));

        $this->assertSame('[Simple.Entity]', $this->grapher->generateFromMetadata(array($class)));
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawSimpleEntityWithFields()
    {
        $class = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class->expects($this->any())->method('getName')->will($this->returnValue('Simple\\Entity'));
        $class->expects($this->any())->method('getFieldNames')->will($this->returnValue(array('a', 'b', 'c')));
        $class->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));
        $class->expects($this->any())->method('isIdentifier')->will(
            $this->returnCallback(
                function ($field) {
                    return $field === 'a';
                }
            )
        );

        $this->assertSame('[Simple.Entity|+a;b;c]', $this->grapher->generateFromMetadata(array($class)));
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawOneToOneUniDirectionalAssociation()
    {
        $class1 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('b')));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('B'));
        $class1->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => true,
            'mappedBy'      => null,
            'inversedBy'    =>null
        )));

        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $class2 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class2->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $this->assertSame('[A]-b 1>[B]', $this->grapher->generateFromMetadata(array($class1, $class2)));
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawOneToOneBiDirectionalAssociation()
    {
        $class1 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('b')));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('B'));
        $class1->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => true,
            'mappedBy'      => null,
            'inversedBy'    =>'a'
        )));
        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $class2 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class2->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('a')));
        $class2->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('A'));
        $class2->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => false,
            'mappedBy'      => 'b',
            'inversedBy'    => null
        )));
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(true));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $class2->expects($this->any())->method('getAssociationMappedByTargetField')->will($this->returnValue('b'));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $this->assertSame('[A]<>a 1-b 1>[B]', $this->grapher->generateFromMetadata(array($class1, $class2)));
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawOneToOneBiDirectionalInverseAssociation()
    {
        $class1 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('b')));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('B'));
        $class1->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => false,
            'mappedBy'      => 'a',
            'inversedBy'    => null
        )));
        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(true));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $class1->expects($this->any())->method('getAssociationMappedByTargetField')->will($this->returnValue('a'));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $class2 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class2->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('a')));
        $class2->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('A'));
        $class2->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => true,
            'mappedBy'      => null,
            'inversedBy'    => 'a'
        )));
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));
        $expected = "[A]<a 1-b 1<>[B]";
        $this->assertSame($expected, $this->grapher->generateFromMetadata(array($class1, $class2)));
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawOneToManyBiDirectionalAssociation()
    {
        $class1 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('b')));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('B'));
        $class1->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => true,
            'mappedBy'      => null,
            'inversedBy'    => 'a'
        )));
        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $class2 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class2->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('a')));
        $class2->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('A'));
        $class2->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => false,
            'mappedBy'      => 'b',
            'inversedBy'    => null
        )));
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(true));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $class2->expects($this->any())->method('getAssociationMappedByTargetField')->will($this->returnValue('b'));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $this->assertSame('[A]<>a 1-b *>[B]', $this->grapher->generateFromMetadata(array($class1, $class2)));
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawOneToManyBiDirectionalInverseAssociation()
    {
        $class1 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('b')));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('B'));
        $class1->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => true,
            'mappedBy'      => null,
            'inversedBy'    => 'a'
        )));
        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $class2 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class2->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('a')));
        $class2->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('A'));
        $class2->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => false,
            'mappedBy'      => 'b',
            'inversedBy'    => null
        )));
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(true));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class2->expects($this->any())->method('getAssociationMappedByTargetField')->will($this->returnValue('b'));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $this->assertSame('[A]<>a *-b 1>[B]', $this->grapher->generateFromMetadata(array($class1, $class2)));
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawManyToManyUniDirectionalAssociation()
    {
        $class1 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('b')));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('B'));
        $class1->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => true,
            'mappedBy'      => null,
            'inversedBy'    => null
        )));
        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $class2 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class2->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $this->assertSame('[A]-b *>[B]', $this->grapher->generateFromMetadata(array($class1, $class2)));
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawManyToManyUniDirectionalInverseAssociation()
    {
        $class1 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $class2 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class2->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('a')));
        $class2->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('A'));
        $class2->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(null));
        $class2->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => true,
            'mappedBy'      => null,
            'inversedBy'    => null
        )));
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $this->assertSame('[A],[B]-a *>[A]', $this->grapher->generateFromMetadata(array($class1, $class2)));
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawManyToManyBiDirectionalAssociation()
    {
        $class1 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('b')));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('B'));
        $class1->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => true,
            'mappedBy'      => null,
            'inversedBy'    => 'a'
        )));
        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $class2 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class2->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('a')));
        $class2->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => false,
            'mappedBy'      => 'a',
            'inversedBy'    => null
        )));
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(true));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class2->expects($this->any())->method('getAssociationMappedByTargetField')->will($this->returnValue('b'));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $this->assertSame('[A]<>a *-b *>[B]', $this->grapher->generateFromMetadata(array($class1, $class2)));
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawManyToManyBiDirectionalInverseAssociation()
    {
        $class1 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('b')));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('B'));
        $class1->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => false,
            'mappedBy'      => 'a',
            'inversedBy'    => null
        )));
        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(true));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class1->expects($this->any())->method('getAssociationMappedByTargetField')->will($this->returnValue('a'));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $class2 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class2->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('a')));
        $class2->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('A'));
        $class2->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => true,
            'mappedBy'      => null,
            'inversedBy'    => 'b'
        )));
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $this->assertSame('[A]<a *-b *<>[B]', $this->grapher->generateFromMetadata(array($class1, $class2)));
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawManyToManyAssociationWithoutKnownInverseSide()
    {
        $class1 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('b')));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('B'));
        $class1->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => true,
            'mappedBy'      => null,
            'inversedBy'    => null
        )));
        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $this->assertSame('[A]<>-b *>[B]', $this->grapher->generateFromMetadata(array($class1)));
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawInheritance()
    {
        $class1 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class2 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $child  = get_class($this->getMock('stdClass'));
        $class1->expects($this->any())->method('getName')->will($this->returnValue('stdClass'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));
        $class2->expects($this->any())->method('getName')->will($this->returnValue($child));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $this->assertSame(
            '[stdClass]^[' . str_replace('\\', '.', $child) . ']',
            $this->grapher->generateFromMetadata(array($class2, $class1))
        );
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawInheritedFields()
    {
        $class1 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class2 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $child  = get_class($this->getMock('stdClass'));

        $class1->expects($this->any())->method('getName')->will($this->returnValue('stdClass'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(array('inherited')));

        $class2->expects($this->any())->method('getName')->will($this->returnValue($child));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue(array('inherited', 'field2')));

        $this->assertSame(
            '[stdClass|inherited]^[' . str_replace('\\', '.', $child) . '|field2]',
            $this->grapher->generateFromMetadata(array($class2, $class1))
        );
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawInheritedAssociations()
    {
        $class1 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class2 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class3 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class4 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $child  = get_class($this->getMock('stdClass'));

        $class1->expects($this->any())->method('getName')->will($this->returnValue('stdClass'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('a')));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('A'));

        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $class2->expects($this->any())->method('getName')->will($this->returnValue($child));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('a', 'b')));
        $class2
            ->expects($this->any())
            ->method('getAssociationTargetClass')
            ->will(
                $this->returnCallback(
                    function ($assoc) {
                        return strtoupper($assoc);
                    }
                )
            );
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $class3->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class3->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));
        $class3->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $class4->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class4->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));
        $class4->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $childName = str_replace('\\', '.', $child);
        $this->assertSame(
            '[stdClass]<>-a *>[A],[stdClass]^[' . $childName . '],[' . $childName . ']<>-b *>[B]',
            $this->grapher->generateFromMetadata(array($class1, $class2))
        );
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     * @dataProvider injectMultipleRelationsWithBothBiAndMonoDirectional
     */
    public function testDrawMultipleClassRelatedBothBiAndMonoDirectional($class1, $class2, $class3, $expected)
    {
        $this->assertSame(
            $expected,
            $this->grapher->generateFromMetadata(array($class1, $class2,$class3))
        );
    }

    /**
     * dataProvider to inject classes in every possible order into the test
     *     testDrawMultipleClassRelatedBothBiAndMonoDirectional
     *
     * @return array
     */
    public function injectMultipleRelationsWithBothBiAndMonoDirectional()
    {
        $class1 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('c')));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('C'));
        $class1->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => true,
            'mappedBy'      => null,
            'inversedBy'    => null
        )));
        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));


        $class2 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class2->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('c')));
        $class2->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('C'));
        $class2->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => true,
            'mappedBy'      => null,
            'inversedBy'    => 'b'
        )));
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));


        $class3 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class3->expects($this->any())->method('getName')->will($this->returnValue('C'));
        $class3->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('b')));
        $class3->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('B'));
        $class3->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => false,
            'mappedBy'      => 'c',
            'inversedBy'    => null
        )));


        $class3->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(true));
        $class3->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class3->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        return array(
            array($class1, $class2, $class3, '[A]-c 1>[C],[B]<>b *-c 1>[C]'),
            array($class1, $class3, $class2, '[A]-c 1>[C],[C]<c 1-b *<>[B]'),
            array($class2, $class1, $class3, '[B]<>b *-c 1>[C],[A]-c 1>[C]'),
            array($class2, $class3, $class1, '[B]<>b *-c 1>[C],[A]-c 1>[C]'),
            array($class3, $class1, $class2, '[C]<c 1-b *<>[B],[A]-c 1>[C]'),
            array($class3, $class2, $class1, '[C]<c 1-b *<>[B],[A]-c 1>[C]')
        );
    }

    /**
     * To mock getAssociationTargetClass method with args
     *
     * @param  string $a
     * @return string
     */
    public function getAssociationTargetClassMock($a)
    {
        return strtoupper($a);
    }

    /**
     * @return array
     */
    public function injectTwoClassesWithTwoDifferentRelationsOneToManyBidirectionnal()
    {
        $classAB = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $classAB->expects($this->any())->method('getName')->will($this->returnValue('AB'));
        $classAB->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('c','d')));
        $classAB
            ->expects($this->any())
            ->method('getAssociationTargetClass')
            ->with($this->logicalOr($this->equalTo('c'), $this->equalTo('d')))
            ->will($this->returnCallback(array($this, 'getAssociationClassMock')));

        $classAB
            ->expects($this->any())
            ->method('getAssociationMapping')
            ->with($this->logicalOr(
                $this->equalTo('c'),
                $this->equalTo('d')
            ))
            ->will($this->returnCallback(array($this, 'getAssociationMappingMock')));


        $classAB->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(true));
        $classAB->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $classAB->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $classCD = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $classCD->expects($this->any())->method('getName')->will($this->returnValue('CD'));
        $classCD->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('a','b')));
        $classCD
            ->expects($this->any())
            ->method('getAssociationTargetClass')
            ->with($this->logicalOr($this->equalTo('a'), $this->equalTo('b')))
            ->will($this->returnCallback(array($this, 'getAssociationClassMock')));
        $classCD
            ->expects($this->any())
            ->method('getAssociationMapping')
            ->with($this->logicalOr(
                $this->equalTo('a'),
                $this->equalTo('b')
            ))
            ->will($this->returnCallback(array($this, 'getAssociationMappingMock')));
        $classCD->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $classCD->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $classCD->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        return array(
            array($classAB, $classCD, "[AB]<a 1-c *<>[CD],[AB]<b 1-d *<>[CD]"),
            array($classCD, $classAB, "[CD]<>c *-a 1>[AB],[CD]<>d *-b 1>[AB]"),
        );
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     * @dataProvider injectTwoClassesWithTwoDifferentRelationsOneToManyBidirectionnal
     */
    public function testMultipleRelationsManyToOneBeetweenTwoSameClasses($class1, $class2, $expected)
    {
        $this->assertSame(
            $expected,
            $this->grapher->generateFromMetadata(array($class1, $class2))
        );
    }

    public function getAssociationClassMock($a)
    {
        switch ($a) {
            case 'a':
            case 'b':
                return 'AB';
                break;
            case 'c':
            case 'd':
                return 'CD';
                break;
        }
        return false;
    }

    public function getAssociationMappingMock($a)
    {
        switch ($a) {
            case 'a':
                $return = array(
                    'isOwningSide' => true,
                    'mappedBy'      => null,
                    'inversedBy'    => 'c'
                );
                break;
            case 'b':
                $return = array(
                    'isOwningSide' => true,
                    'mappedBy'      => null,
                    'inversedBy'    => 'd'
                );
                break;
            case 'c':
                $return = array(
                    'isOwningSide' => false,
                    'mappedBy'      => 'a',
                    'inversedBy'    => null
                );
                break;
            case 'd':
                $return = array(
                    'isOwningSide' => false,
                    'mappedBy'      => 'b',
                    'inversedBy'    => null
                );
                break;
            default:
                $return = false;
        }
        return $return;
    }
}
