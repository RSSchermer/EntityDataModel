<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel\Tests\Type;

use Rolab\EntityDataModel\Type\EntityType;
use Rolab\EntityDataModel\Exception\InvalidArgumentException;

/**
 * @covers EntityType
 */
class EntityTypeTest extends ComplexTypeTestCase
{
    protected $customerReflectionClassFixture;

    protected function setUp()
    {
        $this->customerReflectionClassFixture = new \ReflectionClass('Rolab\EntityDataModel\Tests\Fixtures\Customer');
    }

    public function testConstructor()
    {
        $keyPropertyDescription = $this->buildKeyPropertyDescriptionStub('Id');
        $structuralPropertyDescription = $this->buildStructuralPropertyDescriptionStub('Name');
        $ETagPropertyDescription = $this->buildETagPropertyDescriptionStub('LastChange');

        $entityType = new EntityType('Customer', $this->customerReflectionClassFixture, array(
            $keyPropertyDescription,
            $structuralPropertyDescription,
            $ETagPropertyDescription,
        ));

        $this->assertEquals('Customer', $entityType->getName());
        $this->assertSame($this->customerReflectionClassFixture, $entityType->getReflection());
        $this->assertEquals('Rolab\EntityDataModel\Tests\Fixtures\Customer', $entityType->getClassName());
        $this->assertCount(4, $entityType->getPropertyDescriptions());
        $this->assertCount(3, $entityType->getStructuralPropertyDescriptions());
        $this->assertCount(1, $entityType->getKeyPropertyDescriptions());
        $this->assertCount(1, $entityType->getETagPropertyDescriptions());
        $this->assertCount(0, $entityType->getNavigationPropertyDescriptions());
        $this->assertContains($keyPropertyDescription, $entityType->getPropertyDescriptions());
        $this->assertContains($structuralPropertyDescription, $entityType->getPropertyDescriptions());
        $this->assertContains($ETagPropertyDescription, $entityType->getPropertyDescriptions());
        $this->assertContains($keyPropertyDescription, $entityType->getKeyPropertyDescriptions());
        $this->assertContains($structuralPropertyDescription, $entityType->getStructuralPropertyDescriptions());
        $this->assertContains($keyPropertyDescription, $entityType->getStructuralPropertyDescriptions());
        $this->assertContains($ETagPropertyDescription, $entityType->getStructuralPropertyDescriptions());
        $this->assertContains($ETagPropertyDescription, $entityType->getETagPropertyDescriptions());

        return $entityType;
    }

    /**
     * @depends testConstructor
     */
    public function testConstructorWithBaseEntityType(EntityType $parentEntityType)
    {
        $additionalPropertyDescription = $this->buildStructuralPropertyDescriptionStub('AdditionalProperty');

        $childEntityType = new EntityType(
            'ChildType',
            $this->customerReflectionClassFixture,
            array($additionalPropertyDescription),
            $parentEntityType
        );

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

        new EntityType(
            'Customer',
            $this->customerReflectionClassFixture,
            array($structuralPropertyDescription)
        );
    }

    /**
     * @depends testConstructor
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnConstructionWithBothKeyPropertyAndBaseType(EntityType $parentEntityType)
    {
        $keyPropertyDescription = $this->buildKeyPropertyDescriptionStub('AdditionalKeyProperty');

        new EntityType(
            'Customer',
            $this->customerReflectionClassFixture,
            array($keyPropertyDescription),
            $parentEntityType
        );
    }

    /**
     * @depends testConstructor
     */
    public function testIsSubTypeOf(EntityType $parentEntityType) {
        $additionalPropertyDescription = $this->buildStructuralPropertyDescriptionStub('AdditionalProperty');

        $childEntityType = new EntityType(
            'ChildType',
            $this->customerReflectionClassFixture,
            array($additionalPropertyDescription),
            $parentEntityType
        );

        $otherEntityType = new EntityType(
            'OtherType',
            $this->customerReflectionClassFixture,
            array($this->buildKeyPropertyDescriptionStub('Key'))
        );

        $this->assertTrue($childEntityType->isSubTypeOf($childEntityType));
        $this->assertTrue($childEntityType->isSubTypeOf($parentEntityType));
        $this->assertFalse($childEntityType->isSubTypeOf($otherEntityType));
    }

    /**
     * @depends testConstructor
     */
    public function testAddStructuralPropertyDescriptionWithStructuralPropertyDescription(EntityType $entityType)
    {
        $structuralPropertyDescription = $this->buildStructuralPropertyDescriptionStub('Structural');

        $entityType->addStructuralPropertyDescription($structuralPropertyDescription);

        $this->assertContains($structuralPropertyDescription, $entityType->getPropertyDescriptions());
        $this->assertContains($structuralPropertyDescription, $entityType->getStructuralPropertyDescriptions());
        $this->assertNotContains($structuralPropertyDescription, $entityType->getETagPropertyDescriptions());
        $this->assertNotContains($structuralPropertyDescription, $entityType->getKeyPropertyDescriptions());
        $this->assertNotContains($structuralPropertyDescription, $entityType->getNavigationPropertyDescriptions());
    }

    /**
     * @depends testConstructor
     */
    public function testAddStructuralPropertyDescriptionWithKeyPropertyDescription(EntityType $entityType)
    {
        $keyPropertyDescription = $this->buildKeyPropertyDescriptionStub('Key');

        $entityType->addStructuralPropertyDescription($keyPropertyDescription);

        $this->assertContains($keyPropertyDescription, $entityType->getPropertyDescriptions());
        $this->assertContains($keyPropertyDescription, $entityType->getStructuralPropertyDescriptions());
        $this->assertContains($keyPropertyDescription, $entityType->getKeyPropertyDescriptions());
        $this->assertNotContains($keyPropertyDescription, $entityType->getETagPropertyDescriptions());
        $this->assertNotContains($keyPropertyDescription, $entityType->getNavigationPropertyDescriptions());
    }

    /**
     * @depends testConstructor
     */
    public function testAddStructuralPropertyDescriptionWithETagPropertyDescription(EntityType $entityType)
    {
        $eTagPropertyDescription = $this->buildETagPropertyDescriptionStub('ETag');

        $entityType->addStructuralPropertyDescription($eTagPropertyDescription);

        $this->assertContains($eTagPropertyDescription, $entityType->getPropertyDescriptions());
        $this->assertContains($eTagPropertyDescription, $entityType->getStructuralPropertyDescriptions());
        $this->assertContains($eTagPropertyDescription, $entityType->getETagPropertyDescriptions());
        $this->assertNotContains($eTagPropertyDescription, $entityType->getKeyPropertyDescriptions());
        $this->assertNotContains($eTagPropertyDescription, $entityType->getNavigationPropertyDescriptions());
    }

    /**
     * @depends testConstructor
     */
    public function testAddNavigationPropertyDescription(EntityType $entityType)
    {
        $navigationPropertyDescription = $this->buildNavigationPropertyDescriptionStub('Navigation');

        $entityType->addNavigationPropertyDescription($navigationPropertyDescription);

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
    public function testExceptionOnAddNavigationPropertyDescriptionWithExistingName(EntityType $entityType)
    {
        $navigationPropertyDescription = $this->buildNavigationPropertyDescriptionStub('Name');

        $entityType->addNavigationPropertyDescription($navigationPropertyDescription);
    }

    protected function buildPrimitivePropertyDescriptionStub($name, $key = false, $eTag = false)
    {
        $propertyDescriptionStub = $this->getMockBuilder('Rolab\EntityDataModel\Type\PrimitivePropertyDescription')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $propertyDescriptionStub->method('getName')->willReturn($name);
        $propertyDescriptionStub->method('isPartOfKey')->willReturn($key);
        $propertyDescriptionStub->method('isPartOfETag')->willReturn($eTag);

        return $propertyDescriptionStub;
    }

    protected function buildKeyPropertyDescriptionStub($name)
    {
        return $this->buildPrimitivePropertyDescriptionStub($name, true);
    }

    protected function buildETagPropertyDescriptionStub($name)
    {
        return $this->buildPrimitivePropertyDescriptionStub($name, false, true);
    }

    protected function buildNavigationPropertyDescriptionStub($name)
    {
        $propertyDescriptionStub = $this->getMockBuilder('Rolab\EntityDataModel\Type\NavigationPropertyDescription')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $propertyDescriptionStub->method('getName')->willReturn($name);

        return $propertyDescriptionStub;
    }
}
