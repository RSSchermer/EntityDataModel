<?php

/*
 * This file is part of the Rolab Entity Data Model library.
 *
 * (c) Roland Schermer <roland0507@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rolab\EntityDataModel\Tests\Type;

use Rolab\EntityDataModel\Tests\EntityDataModelTestCase;

use Rolab\EntityDataModel\Type\EntityType;

/**
 * @covers EntityType
 */
class EntityTypeTest extends EntityDataModelTestCase
{
    protected $personReflectionClassFixture;

    protected function setUp()
    {
        $this->personReflectionClassFixture = new \ReflectionClass('Rolab\EntityDataModel\Tests\Fixtures\Person');
    }

    public function testConstructor()
    {
        $keyPropertyDescription = $this->buildKeyPropertyDescriptionStub('IdNumber');
        $structuralPropertyDescription = $this->buildStructuralPropertyDescriptionStub('Name');
        $ETagPropertyDescription = $this->buildETagPropertyDescriptionStub('LastChange');
        $navigationPropertyDescription = $this->buildNavigationPropertyDescriptionStub('Car');

        $entityType = new EntityType('PersonType', $this->personReflectionClassFixture, array(
            $keyPropertyDescription,
            $structuralPropertyDescription,
            $ETagPropertyDescription,
            $navigationPropertyDescription,
        ));

        $this->assertEquals('PersonType', $entityType->getName());
        $this->assertSame($this->personReflectionClassFixture, $entityType->getReflection());
        $this->assertEquals('Rolab\EntityDataModel\Tests\Fixtures\Person', $entityType->getClassName());
        $this->assertCount(4, $entityType->getPropertyDescriptions());
        $this->assertCount(3, $entityType->getStructuralPropertyDescriptions());
        $this->assertCount(1, $entityType->getKeyPropertyDescriptions());
        $this->assertCount(1, $entityType->getETagPropertyDescriptions());
        $this->assertCount(1, $entityType->getNavigationPropertyDescriptions());
        $this->assertContains($keyPropertyDescription, $entityType->getPropertyDescriptions());
        $this->assertContains($structuralPropertyDescription, $entityType->getPropertyDescriptions());
        $this->assertContains($ETagPropertyDescription, $entityType->getPropertyDescriptions());
        $this->assertContains($navigationPropertyDescription, $entityType->getPropertyDescriptions());
        $this->assertContains($keyPropertyDescription, $entityType->getKeyPropertyDescriptions());
        $this->assertContains($structuralPropertyDescription, $entityType->getStructuralPropertyDescriptions());
        $this->assertContains($keyPropertyDescription, $entityType->getStructuralPropertyDescriptions());
        $this->assertContains($ETagPropertyDescription, $entityType->getStructuralPropertyDescriptions());
        $this->assertContains($ETagPropertyDescription, $entityType->getETagPropertyDescriptions());
        $this->assertContains($navigationPropertyDescription, $entityType->getNavigationPropertyDescriptions());

        return $entityType;
    }

    /**
     * @depends testConstructor
     */
    public function testConstructorWithBaseEntityType(EntityType $parentEntityType)
    {
        $additionalPropertyDescription = $this->buildStructuralPropertyDescriptionStub('AdditionalProperty');

        $childEntityType = new EntityType('ChildType', $this->personReflectionClassFixture,
            array($additionalPropertyDescription), $parentEntityType);

        $this->assertCount(5, $childEntityType->getPropertyDescriptions());
        $this->assertCount(4, $childEntityType->getStructuralPropertyDescriptions());
        $this->assertCount(1, $childEntityType->getKeyPropertyDescriptions());
        $this->assertCount(1, $childEntityType->getETagPropertyDescriptions());
        $this->assertCount(1, $childEntityType->getNavigationPropertyDescriptions());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnConstructionWithOutBothKeyPropertyAndBaseType()
    {
        $structuralPropertyDescription = $this->buildStructuralPropertyDescriptionStub('AdditionalProperty');

        $childEntityType = new EntityType('PersonType', $this->personReflectionClassFixture,
            array($structuralPropertyDescription));
    }

    /**
     * @depends testConstructor
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnConstructionWithBothKeyPropertyAndBaseType(EntityType $parentEntityType)
    {
        $keyPropertyDescription = $this->buildKeyPropertyDescriptionStub('AdditionalKeyProperty');

        $childEntityType = new EntityType('PersonType', $this->personReflectionClassFixture,
            array($keyPropertyDescription), $parentEntityType);
    }

    /**
     * @depends testConstructor
     */
    public function testAddPropertyDescriptionWithStructuralPropertyDescription(EntityType $entityType)
    {
        $structuralPropertyDescription = $this->buildStructuralPropertyDescriptionStub('Structural');

        $entityType->addPropertyDescription($structuralPropertyDescription);

        $this->assertContains($structuralPropertyDescription, $entityType->getPropertyDescriptions());
        $this->assertContains($structuralPropertyDescription, $entityType->getStructuralPropertyDescriptions());
        $this->assertNotContains($structuralPropertyDescription, $entityType->getETagPropertyDescriptions());
        $this->assertNotContains($structuralPropertyDescription, $entityType->getKeyPropertyDescriptions());
        $this->assertNotContains($structuralPropertyDescription, $entityType->getNavigationPropertyDescriptions());
    }

    /**
     * @depends testConstructor
     */
    public function testAddPropertyDescriptionWithETagPropertyDescription(EntityType $entityType)
    {
        $eTagPropertyDescription = $this->buildETagPropertyDescriptionStub('ETag');

        $entityType->addPropertyDescription($eTagPropertyDescription);

        $this->assertContains($eTagPropertyDescription, $entityType->getPropertyDescriptions());
        $this->assertContains($eTagPropertyDescription, $entityType->getStructuralPropertyDescriptions());
        $this->assertContains($eTagPropertyDescription, $entityType->getETagPropertyDescriptions());
        $this->assertNotContains($eTagPropertyDescription, $entityType->getKeyPropertyDescriptions());
        $this->assertNotContains($eTagPropertyDescription, $entityType->getNavigationPropertyDescriptions());
    }

    /**
     * @depends testConstructor
     */
    public function testAddPropertyDescriptionWithNavigationPropertyDescription(EntityType $entityType)
    {
        $navigationPropertyDescription = $this->buildNavigationPropertyDescriptionStub('Navigation');

        $entityType->addPropertyDescription($navigationPropertyDescription);

        $this->assertContains($navigationPropertyDescription, $entityType->getPropertyDescriptions());
        $this->assertNotContains($navigationPropertyDescription, $entityType->getStructuralPropertyDescriptions());
        $this->assertNotContains($navigationPropertyDescription, $entityType->getETagPropertyDescriptions());
        $this->assertNotContains($navigationPropertyDescription, $entityType->getKeyPropertyDescriptions());
        $this->assertContains($navigationPropertyDescription, $entityType->getNavigationPropertyDescriptions());
    }

    /**
     * @depends testConstructor
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnAddPropertyDescriptionWithExistingName(EntityType $entityType)
    {
        $navigationPropertyDescription = $this->buildNavigationPropertyDescriptionStub('Name');

        $entityType->addPropertyDescription($navigationPropertyDescription);
    }

    /**
     * @depends testConstructor
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnAddKeyPropertyDescription(EntityType $entityType)
    {
        $keyPropertyDescription = $this->buildKeyPropertyDescriptionStub('Key');

        $entityType->addPropertyDescription($keyPropertyDescription);
    }

    /**
     * @depends testConstructor
     */
    public function testRemovePropertyDescriptionForStructuralPropertyDescription(EntityType $entityType)
    {
        $this->assertNotEmpty($entityType->getPropertyDescriptionByName('Name'));

        $count = count($entityType->getStructuralPropertyDescriptions());
        $entityType->removePropertyDescription('Name');

        $this->assertEmpty($entityType->getPropertyDescriptionByName('Name'));
        $this->assertCount($count - 1, $entityType->getStructuralPropertyDescriptions());
    }

    /**
     * @depends testConstructor
     */
    public function testRemovePropertyDescriptionForETagPropertyDescription(EntityType $entityType)
    {
        $this->assertNotEmpty($entityType->getPropertyDescriptionByName('LastChange'));

        $count = count($entityType->getETagPropertyDescriptions());
        $entityType->removePropertyDescription('LastChange');

        $this->assertEmpty($entityType->getPropertyDescriptionByName('LastChange'));
        $this->assertCount($count - 1, $entityType->getETagPropertyDescriptions());
    }

    /**
     * @depends testConstructor
     */
    public function testRemovePropertyDescriptionForNavigationPropertyDescription(EntityType $entityType)
    {
        $this->assertNotEmpty($entityType->getPropertyDescriptionByName('Car'));

        $count = count($entityType->getNavigationPropertyDescriptions());
        $entityType->removePropertyDescription('Car');

        $this->assertEmpty($entityType->getPropertyDescriptionByName('Car'));
        $this->assertCount($count - 1, $entityType->getNavigationPropertyDescriptions());
    }

    protected function buildKeyPropertyDescriptionStub($name)
    {
        return $this->buildPropertyDescriptionStub($name, 'Rolab\EntityDataModel\Type\KeyPropertyDescription');
    }

    protected function buildETagPropertyDescriptionStub($name)
    {
        return $this->buildPropertyDescriptionStub($name, 'Rolab\EntityDataModel\Type\ETagPropertyDescription');
    }

    protected function buildNavigationPropertyDescriptionStub($name)
    {
        return $this->buildPropertyDescriptionStub($name, 'Rolab\EntityDataModel\Type\NavigationPropertyDescription');
    }

    protected function buildStructuralPropertyDescriptionStub($name)
    {
        return $this->buildPropertyDescriptionStub($name, 'Rolab\EntityDataModel\Type\StructuralPropertyDescription');
    }

    protected function buildPropertyDescriptionStub($name, $className)
    {
        $propertyDescriptionStub = $this->getMockBuilder($className)
            ->disableOriginalConstructor()
            ->setMethods(array('getName'))
            ->getMockForAbstractClass();

        $propertyDescriptionStub->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name));

        return $propertyDescriptionStub;
    }
}
