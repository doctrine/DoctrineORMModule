<?php

namespace DoctrineORMModuleTest\Yuml;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use DoctrineORMModule\Yuml\MetadataGrapher;
use PHPUnit\Framework\TestCase;

use function get_class;
use function str_replace;
use function strtoupper;

/**
 * Tests for the metadata to string converter
 *
 * @link    http://www.doctrine-project.org/
 */
class MetadataGrapherTest extends TestCase
{
    /** @var MetadataGrapher */
    protected $grapher;

    public function setUp(): void
    {
        parent::setUp();

        $this->grapher = new MetadataGrapher();
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawSimpleEntity(): void
    {
        $class = $this->createMock(ClassMetadata::class);
        $class->expects($this->any())->method('getName')->will($this->returnValue('Simple\\Entity'));
        $class->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));
        $class->expects($this->any())->method('getAssociationNames')->will($this->returnValue([]));

        $this->assertSame('[Simple.Entity]', $this->grapher->generateFromMetadata([$class]));
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawSimpleEntityWithFields(): void
    {
        $class = $this->createMock(ClassMetadata::class);
        $class->expects($this->any())->method('getName')->will($this->returnValue('Simple\\Entity'));
        $class->expects($this->any())->method('getFieldNames')->will($this->returnValue(['a', 'b', 'c']));
        $class->expects($this->any())->method('getAssociationNames')->will($this->returnValue([]));
        $class->expects($this->any())->method('isIdentifier')->will(
            $this->returnCallback(
                static function ($field) {
                    return $field === 'a';
                }
            )
        );

        $this->assertSame('[Simple.Entity|+a;b;c]', $this->grapher->generateFromMetadata([$class]));
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawOneToOneUniDirectionalAssociation(): void
    {
        $class1 = $this->createMock(ClassMetadata::class);
        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['b']));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('B'));
        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $class2 = $this->createMock(ClassMetadata::class);
        $class2->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue([]));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $this->assertSame('[A]-b 1>[B]', $this->grapher->generateFromMetadata([$class1, $class2]));
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawOneToOneBiDirectionalAssociation(): void
    {
        $class1 = $this->createMock(ClassMetadata::class);
        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['b']));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('B'));
        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $class2 = $this->createMock(ClassMetadata::class);
        $class2->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['a']));
        $class2->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('A'));
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(true));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $class2->expects($this->any())->method('getAssociationMappedByTargetField')->will($this->returnValue('b'));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $this->assertSame('[A]<>a 1-b 1>[B]', $this->grapher->generateFromMetadata([$class1, $class2]));
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawOneToOneBiDirectionalInverseAssociation(): void
    {
        $class1 = $this->createMock(ClassMetadata::class);
        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['b']));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('B'));
        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(true));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $class1->expects($this->any())->method('getAssociationMappedByTargetField')->will($this->returnValue('a'));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $class2 = $this->createMock(ClassMetadata::class);
        $class2->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['a']));
        $class2->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('A'));
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $this->assertSame('[A]<a 1-b 1<>[B]', $this->grapher->generateFromMetadata([$class1, $class2]));
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawOneToManyBiDirectionalAssociation(): void
    {
        $class1 = $this->createMock(ClassMetadata::class);
        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['b']));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('B'));
        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $class2 = $this->createMock(ClassMetadata::class);
        $class2->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['a']));
        $class2->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('A'));
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(true));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $class2->expects($this->any())->method('getAssociationMappedByTargetField')->will($this->returnValue('b'));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $this->assertSame('[A]<>a 1-b *>[B]', $this->grapher->generateFromMetadata([$class1, $class2]));
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawOneToManyBiDirectionalInverseAssociation(): void
    {
        $class1 = $this->createMock(ClassMetadata::class);
        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['b']));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('B'));
        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $class2 = $this->createMock(ClassMetadata::class);
        $class2->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['a']));
        $class2->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('A'));
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(true));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class2->expects($this->any())->method('getAssociationMappedByTargetField')->will($this->returnValue('b'));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $this->assertSame('[A]<>a *-b 1>[B]', $this->grapher->generateFromMetadata([$class1, $class2]));
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawManyToManyUniDirectionalAssociation(): void
    {
        $class1 = $this->createMock(ClassMetadata::class);
        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['b']));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('B'));
        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $class2 = $this->createMock(ClassMetadata::class);
        $class2->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue([]));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $this->assertSame('[A]-b *>[B]', $this->grapher->generateFromMetadata([$class1, $class2]));
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawManyToManyUniDirectionalInverseAssociation(): void
    {
        $class1 = $this->createMock(ClassMetadata::class);
        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue([]));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $class2 = $this->createMock(ClassMetadata::class);
        $class2->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['a']));
        $class2->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('A'));
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $this->assertSame('[A],[B]-a *>[A]', $this->grapher->generateFromMetadata([$class1, $class2]));
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawManyToManyBiDirectionalAssociation(): void
    {
        $class1 = $this->createMock(ClassMetadata::class);
        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['b']));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('B'));
        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $class2 = $this->createMock(ClassMetadata::class);
        $class2->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['a']));
        $class2->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('A'));
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(true));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class2->expects($this->any())->method('getAssociationMappedByTargetField')->will($this->returnValue('b'));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $this->assertSame('[A]<>a *-b *>[B]', $this->grapher->generateFromMetadata([$class1, $class2]));
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawManyToManyBiDirectionalInverseAssociation(): void
    {
        $class1 = $this->createMock(ClassMetadata::class);
        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['b']));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('B'));
        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(true));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class1->expects($this->any())->method('getAssociationMappedByTargetField')->will($this->returnValue('a'));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $class2 = $this->createMock(ClassMetadata::class);
        $class2->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['a']));
        $class2->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('A'));
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $this->assertSame('[A]<a *-b *<>[B]', $this->grapher->generateFromMetadata([$class1, $class2]));
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawManyToManyAssociationWithoutKnownInverseSide(): void
    {
        $class1 = $this->createMock(ClassMetadata::class);
        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['b']));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('B'));
        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $this->assertSame('[A]<>-b *>[B]', $this->grapher->generateFromMetadata([$class1]));
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawInheritance(): void
    {
        $class1 = $this->createMock(ClassMetadata::class);
        $class2 = $this->createMock(ClassMetadata::class);
        $child  = get_class($this->createMock('stdClass'));
        $class1->expects($this->any())->method('getName')->will($this->returnValue('stdClass'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue([]));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));
        $class2->expects($this->any())->method('getName')->will($this->returnValue($child));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue([]));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $this->assertSame(
            '[stdClass]^[' . str_replace('\\', '.', $child) . ']',
            $this->grapher->generateFromMetadata([$class2, $class1])
        );
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawInheritedFields(): void
    {
        $class1 = $this->createMock(ClassMetadata::class);
        $class2 = $this->createMock(ClassMetadata::class);
        $child  = get_class($this->createMock('stdClass'));

        $class1->expects($this->any())->method('getName')->will($this->returnValue('stdClass'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue([]));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(['inherited']));

        $class2->expects($this->any())->method('getName')->will($this->returnValue($child));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue([]));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue(['inherited', 'field2']));

        $this->assertSame(
            '[stdClass|inherited]^[' . str_replace('\\', '.', $child) . '|field2]',
            $this->grapher->generateFromMetadata([$class2, $class1])
        );
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawInheritedAssociations(): void
    {
        $class1 = $this->createMock(ClassMetadata::class);
        $class2 = $this->createMock(ClassMetadata::class);
        $class3 = $this->createMock(ClassMetadata::class);
        $class4 = $this->createMock(ClassMetadata::class);
        $child  = get_class($this->createMock('stdClass'));

        $class1->expects($this->any())->method('getName')->will($this->returnValue('stdClass'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['a']));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $class2->expects($this->any())->method('getName')->will($this->returnValue($child));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['a', 'b']));
        $class2
            ->expects($this->any())
            ->method('getAssociationTargetClass')
            ->will(
                $this->returnCallback(
                    static function ($assoc) {
                        return strtoupper($assoc);
                    }
                )
            );
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $class3->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class3->expects($this->any())->method('getAssociationNames')->will($this->returnValue([]));
        $class3->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $class4->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class4->expects($this->any())->method('getAssociationNames')->will($this->returnValue([]));
        $class4->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $childName = str_replace('\\', '.', $child);
        $this->assertSame(
            '[stdClass]<>-a *>[A],[stdClass]^[' . $childName . '],[' . $childName . ']<>-b *>[B]',
            $this->grapher->generateFromMetadata([$class1, $class2])
        );
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     * @dataProvider injectMultipleRelationsWithBothBiAndMonoDirectional
     */
    public function testDrawMultipleClassRelatedBothBiAndMonoDirectional(
        ClassMetadata $class1,
        ClassMetadata $class2,
        ClassMetadata $class3,
        string $expected
    ): void {
        $this->assertSame(
            $expected,
            $this->grapher->generateFromMetadata([$class1, $class2, $class3])
        );
    }

    /**
     * dataProvider to inject classes in every order possible into the test
     *     testDrawMultipleClassRelatedBothBiAndMonoDirectional
     *
     * @return list<array{ClassMetadata, ClassMetadata, ClassMetadata, string}>
     */
    public function injectMultipleRelationsWithBothBiAndMonoDirectional(): array
    {
        $class1 = $this->createMock(ClassMetadata::class);
        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['c']));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('C'));
        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $class2 = $this->createMock(ClassMetadata::class);
        $class2->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['c']));
        $class2->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('C'));
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $class3 = $this->createMock(ClassMetadata::class);
        $class3->expects($this->any())->method('getName')->will($this->returnValue('C'));
        $class3->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['b']));
        $class3
            ->expects($this->any())
            ->method('getAssociationTargetClass')
            ->with($this->logicalOr($this->equalTo('b'), $this->equalTo('c')))
            ->will($this->returnCallback([$this, 'getAssociationTargetClassMock']));
        $class3->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(true));
        $class3->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class3->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        return [
            [$class1, $class2, $class3, '[A]-c 1>[C],[B]<>b *-c 1>[C]'],
            [clone $class1, clone $class3, clone $class2, '[A]-c 1>[C],[C]<c 1-b *<>[B]'],
            [clone $class2, clone $class1, clone $class3, '[B]<>b *-c 1>[C],[A]-c 1>[C]'],
            [clone $class2, clone $class3, clone $class1, '[B]<>b *-c 1>[C],[A]-c 1>[C]'],
            [clone $class3, clone $class1, clone $class2, '[C]<c 1-b *<>[B],[A]-c 1>[C]'],
            [clone $class3, clone $class2, clone $class1, '[C]<c 1-b *<>[B],[A]-c 1>[C]'],
        ];
    }

    /**
     * To mock getAssociationTargetClass method with args
     */
    public function getAssociationTargetClassMock(string $a): string
    {
        return strtoupper($a);
    }
}
