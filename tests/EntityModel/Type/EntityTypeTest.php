<?php

declare(strict_types=1);

namespace RSSchermer\Tests\EntityModel\Type;

use RSSchermer\EntityModel\Type\ComplexPropertyDescription;
use RSSchermer\EntityModel\Type\ComplexType;
use RSSchermer\EntityModel\Type\Edm\EdmInt32;
use RSSchermer\EntityModel\Type\Edm\EdmString;
use RSSchermer\EntityModel\Type\EntityType;
use RSSchermer\EntityModel\Exception\InvalidArgumentException;
use RSSchermer\EntityModel\Type\NavigationPropertyDescription;
use RSSchermer\EntityModel\Type\PrimitivePropertyDescription;

/**
 * @covers EntityType
 */
class EntityTypeTest extends ComplexTypeTestCase
{
    protected $customerReflectionClassFixture;

    protected $idPropertyFixture;

    protected $firstNamePropertyFixture;

    protected $lastNamePropertyFixture;

    protected $addressPropertyFixture;

    protected $ordersPropertyFixture;

    protected function setUp()
    {
        $this->customerReflectionClassFixture = new \ReflectionClass('RSSchermer\Tests\EntityModel\Fixtures\Customer');

        $this->idPropertyFixture = new PrimitivePropertyDescription(
            'Id',
            new \ReflectionProperty('RSSchermer\Tests\EntityModel\Fixtures\Customer', 'id'),
            EdmInt32::create(),
            false,
            true,
            true
        );

        $this->firstNamePropertyFixture = new PrimitivePropertyDescription(
            'FirstName',
            new \ReflectionProperty('RSSchermer\Tests\EntityModel\Fixtures\Customer', 'firstName'),
            EdmString::create()
        );

        $this->lastNamePropertyFixture = new PrimitivePropertyDescription(
            'LastName',
            new \ReflectionProperty('RSSchermer\Tests\EntityModel\Fixtures\Customer', 'lastName'),
            EdmString::create()
        );

        $this->addressPropertyFixture = new ComplexPropertyDescription(
            'Address',
            new \ReflectionProperty('RSSchermer\Tests\EntityModel\Fixtures\Customer', 'address'),
            new ComplexType(
                'Address',
                new \ReflectionClass('RSSchermer\Tests\EntityModel\Fixtures\Customer')
            )
        );

        $this->ordersPropertyFixture = new NavigationPropertyDescription(
            'Orders',
            new \ReflectionProperty('RSSchermer\Tests\EntityModel\Fixtures\Customer', 'orders'),
            new EntityType(
                'Order',
                new \ReflectionClass('RSSchermer\Tests\EntityModel\Fixtures\Order'),
                array(new PrimitivePropertyDescription(
                    'Id',
                    new \ReflectionProperty('RSSchermer\Tests\EntityModel\Fixtures\Order', 'id'),
                    EdmInt32::create(),
                    false,
                    true,
                    true
                ))
            ),
            true
        );
    }

    public function testConstructor()
    {
        $entityType = new EntityType('Customer', $this->customerReflectionClassFixture, array(
            $this->idPropertyFixture,
            $this->lastNamePropertyFixture,
            $this->addressPropertyFixture
        ));

        $this->assertEquals('Customer', $entityType->getName());
        $this->assertSame($this->customerReflectionClassFixture, $entityType->getReflection());
        $this->assertEquals('RSSchermer\Tests\EntityModel\Fixtures\Customer', $entityType->getClassName());
        $this->assertCount(3, $entityType->getPropertyDescriptions());
        $this->assertCount(3, $entityType->getStructuralPropertyDescriptions());
        $this->assertCount(1, $entityType->getKeyPropertyDescriptions());
        $this->assertCount(0, $entityType->getNavigationPropertyDescriptions());
        $this->assertContains($this->idPropertyFixture, $entityType->getPropertyDescriptions()->values());
        $this->assertContains($this->lastNamePropertyFixture, $entityType->getPropertyDescriptions()->values());
        $this->assertContains($this->addressPropertyFixture, $entityType->getPropertyDescriptions()->values());
        $this->assertContains($this->idPropertyFixture, $entityType->getKeyPropertyDescriptions()->values());
        $this->assertContains($this->lastNamePropertyFixture, $entityType->getStructuralPropertyDescriptions()->values());
        $this->assertContains($this->addressPropertyFixture, $entityType->getStructuralPropertyDescriptions()->values());
        $this->assertContains($this->idPropertyFixture, $entityType->getStructuralPropertyDescriptions()->values());
        $this->assertNotContains($this->lastNamePropertyFixture, $entityType->getKeyPropertyDescriptions()->values());
        $this->assertNotContains($this->addressPropertyFixture, $entityType->getKeyPropertyDescriptions()->values());

        return $entityType;
    }

    /**
     * @depends testConstructor
     */
    public function testConstructorWithBaseEntityType(EntityType $parentEntityType)
    {
        $childEntityType = new EntityType(
            'ChildType',
            $this->customerReflectionClassFixture,
            array($this->firstNamePropertyFixture),
            $parentEntityType
        );

        $this->assertCount(4, $childEntityType->getPropertyDescriptions());
        $this->assertCount(4, $childEntityType->getStructuralPropertyDescriptions());
        $this->assertCount(1, $childEntityType->getKeyPropertyDescriptions());
        $this->assertCount(0, $childEntityType->getNavigationPropertyDescriptions());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnConstructionWithNeitherKeyPropertyNorBaseType()
    {
        new EntityType(
            'Customer',
            $this->customerReflectionClassFixture,
            array($this->lastNamePropertyFixture)
        );
    }

    /**
     * @depends testConstructor
     */
    public function testIsSubTypeOf(EntityType $parentEntityType) {
        $childEntityType = new EntityType(
            'ChildType',
            $this->customerReflectionClassFixture,
            array($this->firstNamePropertyFixture),
            $parentEntityType
        );

        $otherEntityType = new EntityType(
            'OtherType',
            $this->customerReflectionClassFixture,
            array($this->idPropertyFixture)
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
        $structuralPropertyDescription = $this->firstNamePropertyFixture;

        $entityType->addStructuralPropertyDescription($structuralPropertyDescription);

        $this->assertContains($structuralPropertyDescription, $entityType->getPropertyDescriptions());
        $this->assertContains($structuralPropertyDescription, $entityType->getStructuralPropertyDescriptions());
        $this->assertNotContains($structuralPropertyDescription, $entityType->getKeyPropertyDescriptions());
        $this->assertNotContains($structuralPropertyDescription, $entityType->getNavigationPropertyDescriptions());
    }

    /**
     * @depends testConstructor
     */
    public function testAddStructuralPropertyDescriptionWithKeyPropertyDescription(EntityType $entityType)
    {
        $keyPropertyDescription = new PrimitivePropertyDescription(
            'Key',
            new \ReflectionProperty('RSSchermer\Tests\EntityModel\Fixtures\Customer', 'id'),
            EdmInt32::create(),
            false,
            true,
            true
        );

        $entityType->addStructuralPropertyDescription($keyPropertyDescription);

        $this->assertCount(2, $entityType->getKeyPropertyDescriptions());
        $this->assertContains($keyPropertyDescription, $entityType->getPropertyDescriptions());
        $this->assertContains($keyPropertyDescription, $entityType->getStructuralPropertyDescriptions());
        $this->assertContains($keyPropertyDescription, $entityType->getKeyPropertyDescriptions());
        $this->assertNotContains($keyPropertyDescription, $entityType->getNavigationPropertyDescriptions());
    }

    /**
     * @depends testConstructor
     */
    public function testAddNavigationPropertyDescription(EntityType $entityType)
    {
        $entityType->addNavigationPropertyDescription($this->ordersPropertyFixture);

        $this->assertContains($this->ordersPropertyFixture, $entityType->getPropertyDescriptions());
        $this->assertNotContains($this->ordersPropertyFixture, $entityType->getStructuralPropertyDescriptions());
        $this->assertNotContains($this->ordersPropertyFixture, $entityType->getKeyPropertyDescriptions());
        $this->assertContains($this->ordersPropertyFixture, $entityType->getNavigationPropertyDescriptions());

        return $entityType;
    }

    /**
     * @depends testAddNavigationPropertyDescription
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnAddNavigationPropertyDescriptionWithExistingName(EntityType $entityType)
    {
        $entityType->addNavigationPropertyDescription($this->ordersPropertyFixture);
    }
}
