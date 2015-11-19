<?php

declare(strict_types=1);

namespace RSSchermer\Tests\EntityModel\Type;

use RSSchermer\EntityModel\Type\Edm\EdmInt32;
use RSSchermer\EntityModel\Type\EntityType;
use RSSchermer\EntityModel\Type\NavigationPropertyDescription;
use RSSchermer\EntityModel\Exception\InvalidArgumentException;
use RSSchermer\EntityModel\Exception\RuntimeException;
use RSSchermer\EntityModel\Type\PrimitivePropertyDescription;

/**
 * @covers NavigationPropertyDescription
 */
class NavigationPropertyDescriptionTest extends ResourcePropertyDescriptionTestCase
{
    protected $propertyReflectionFixture;

    protected $customerTypeFixture;

    protected $orderTypeFixture;

    protected function setUp()
    {
        $this->propertyReflectionFixture = new \ReflectionProperty(
            'RSSchermer\Tests\EntityModel\Fixtures\Customer',
            'orders'
        );

        $this->customerTypeFixture = new EntityType(
            'Customer',
            new \ReflectionClass('RSSchermer\Tests\EntityModel\Fixtures\Customer'),
            array(new PrimitivePropertyDescription(
                'Id',
                new \ReflectionProperty('RSSchermer\Tests\EntityModel\Fixtures\Customer', 'id'),
                EdmInt32::create(),
                false,
                true,
                true
            ))
        );

        $this->orderTypeFixture = new EntityType(
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
        );
    }

    public function testConstructor()
    {
        $navigationPropertyDescription = new NavigationPropertyDescription(
            'NavigationProperty',
            $this->propertyReflectionFixture,
            $this->orderTypeFixture
        );

        $this->assertEquals('NavigationProperty', $navigationPropertyDescription->getName());
        $this->assertSame($this->propertyReflectionFixture, $navigationPropertyDescription->getReflection());
        $this->assertSame($this->orderTypeFixture, $navigationPropertyDescription->getPropertyValueType());
        $this->assertFalse($navigationPropertyDescription->isCollection());
        $this->assertTrue($navigationPropertyDescription->isNullable());
        $this->assertEquals(
            $navigationPropertyDescription->getOnDeleteAction(),
            NavigationPropertyDescription::DELETE_ACTION_NONE
        );

        return $navigationPropertyDescription;
    }

    /**
     * @dataProvider invalidNameProvider
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnInvalidName(string $invalidName)
    {
        new NavigationPropertyDescription(
            $invalidName,
            $this->propertyReflectionFixture,
            $this->orderTypeFixture
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnInvalidOnDeleteAction()
    {
        new NavigationPropertyDescription(
            'SomeName',
            $this->propertyReflectionFixture,
            $this->orderTypeFixture,
            false,
            true,
            'something'
        );
    }

    public function testSetPartner()
    {
        $navigationPropertyDescription = new NavigationPropertyDescription(
            'Orders',
            $this->propertyReflectionFixture,
            $this->orderTypeFixture
        );

        $navigationPropertyDescription->setStructuredType($this->customerTypeFixture);

        $partnerNavigationProperty = new NavigationPropertyDescription(
            'Customer',
            new \ReflectionProperty(
                'RSSchermer\Tests\EntityModel\Fixtures\Order',
                'customer'
            ),
            $this->customerTypeFixture
        );

        $partnerNavigationProperty->setStructuredType($this->orderTypeFixture);
        $navigationPropertyDescription->setPartner($partnerNavigationProperty);

        $this->assertSame($partnerNavigationProperty, $navigationPropertyDescription->getPartner()->get());
    }

    /**
     * @expectedException RuntimeException
     */
    public function testExceptionOnSetPartnerWithoutOwnerEntityType()
    {
        $navigationPropertyDescription = new NavigationPropertyDescription(
            'Orders',
            $this->propertyReflectionFixture,
            $this->orderTypeFixture
        );

        $partnerNavigationProperty = new NavigationPropertyDescription(
            'Customer',
            new \ReflectionProperty(
                'RSSchermer\Tests\EntityModel\Fixtures\Order',
                'customer'
            ),
            $this->customerTypeFixture
        );

        $partnerNavigationProperty->setStructuredType($this->orderTypeFixture);
        $navigationPropertyDescription->setPartner($partnerNavigationProperty);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnSetPartnerWithNonMatchingEntityType()
    {

        $navigationPropertyDescription = new NavigationPropertyDescription(
            'Orders',
            $this->propertyReflectionFixture,
            $this->orderTypeFixture
        );

        $navigationPropertyDescription->setStructuredType($this->customerTypeFixture);

        $partnerNavigationProperty = new NavigationPropertyDescription(
            'Customer',
            new \ReflectionProperty(
                'RSSchermer\Tests\EntityModel\Fixtures\Order',
                'customer'
            ),
            $this->customerTypeFixture
        );

        $partnerNavigationProperty->setStructuredType($this->customerTypeFixture);
        $navigationPropertyDescription->setPartner($partnerNavigationProperty);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnSetPartnerWithNonMatchingPropertyValueType()
    {
        $navigationPropertyDescription = new NavigationPropertyDescription(
            'Orders',
            $this->propertyReflectionFixture,
            $this->orderTypeFixture
        );

        $navigationPropertyDescription->setStructuredType($this->customerTypeFixture);

        $partnerNavigationProperty = new NavigationPropertyDescription(
            'Customer',
            new \ReflectionProperty(
                'RSSchermer\Tests\EntityModel\Fixtures\Order',
                'customer'
            ),
            $this->orderTypeFixture
        );

        $partnerNavigationProperty->setStructuredType($this->orderTypeFixture);
        $navigationPropertyDescription->setPartner($partnerNavigationProperty);
    }
}
