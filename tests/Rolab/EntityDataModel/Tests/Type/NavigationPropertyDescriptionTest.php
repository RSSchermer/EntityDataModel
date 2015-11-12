<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel\Tests\Type;

use Rolab\EntityDataModel\Type\NavigationPropertyDescription;
use Rolab\EntityDataModel\Exception\InvalidArgumentException;
use Rolab\EntityDataModel\Exception\RuntimeException;

/**
 * @covers NavigationPropertyDescription
 */
class NavigationPropertyDescriptionTest extends ResourcePropertyDescriptionTestCase
{
    protected $propertyReflectionFixture;

    protected $entityTypeFixture;

    protected function setUp()
    {
        $this->propertyReflectionFixture = new \ReflectionProperty(
            'Rolab\EntityDataModel\Tests\Fixtures\Customer',
            'orders'
        );
    }

    public function testConstructor()
    {
        $entityTypeStub = $this->buildEntityTypeStub();

        $navigationPropertyDescription = new NavigationPropertyDescription(
            'NavigationProperty',
            $this->propertyReflectionFixture,
            $entityTypeStub
        );

        $this->assertEquals('NavigationProperty', $navigationPropertyDescription->getName());
        $this->assertSame($this->propertyReflectionFixture, $navigationPropertyDescription->getReflection());
        $this->assertSame($entityTypeStub, $navigationPropertyDescription->getPropertyValueType());
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
            $this->buildEntityTypeStub('SomeEntity')
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
            $this->buildEntityTypeStub('SomeEntity'),
            false,
            true,
            'something'
        );
    }

    public function testSetPartner()
    {
        $partnerEntityTypeStub = $this->buildEntityTypeStub();

        $navigationPropertyDescription = new NavigationPropertyDescription(
            'NavigationProperty',
            $this->propertyReflectionFixture,
            $partnerEntityTypeStub
        );

        $ownerEntityType = $this->buildEntityTypeStub();
        $navigationPropertyDescription->setStructuredType($ownerEntityType);

        $partnerNavigationProperty = new NavigationPropertyDescription(
            'PartnerProperty',
            $this->propertyReflectionFixture,
            $ownerEntityType
        );

        $partnerNavigationProperty->setStructuredType($partnerEntityTypeStub);
        $navigationPropertyDescription->setPartner($partnerNavigationProperty);

        $this->assertSame($partnerNavigationProperty, $navigationPropertyDescription->getPartner());
    }

    /**
     * @expectedException RuntimeException
     */
    public function testExceptionOnSetPartnerWithoutOwnerEntityType()
    {
        $partnerEntityTypeStub = $this->buildEntityTypeStub();

        $navigationPropertyDescription = new NavigationPropertyDescription(
            'NavigationProperty',
            $this->propertyReflectionFixture,
            $partnerEntityTypeStub
        );

        $ownerEntityType = $this->buildEntityTypeStub();

        $partnerNavigationProperty = new NavigationPropertyDescription(
            'PartnerProperty',
            $this->propertyReflectionFixture,
            $ownerEntityType
        );

        $partnerNavigationProperty->setStructuredType($partnerEntityTypeStub);
        $navigationPropertyDescription->setPartner($partnerNavigationProperty);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnSetPartnerWithNonMatchingEntityType()
    {
        $partnerEntityTypeStub = $this->buildEntityTypeStub();

        $navigationPropertyDescription = new NavigationPropertyDescription(
            'NavigationProperty',
            $this->propertyReflectionFixture,
            $partnerEntityTypeStub
        );

        $ownerEntityType = $this->buildEntityTypeStub();
        $navigationPropertyDescription->setStructuredType($ownerEntityType);

        $partnerNavigationProperty = new NavigationPropertyDescription(
            'PartnerProperty',
            $this->propertyReflectionFixture,
            $ownerEntityType
        );

        $otherEntityType = $this->buildEntityTypeStub();

        $partnerNavigationProperty->setStructuredType($otherEntityType);
        $navigationPropertyDescription->setPartner($partnerNavigationProperty);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnSetPartnerWithNonMatchingPropertyValueType()
    {
        $partnerEntityTypeStub = $this->buildEntityTypeStub();

        $navigationPropertyDescription = new NavigationPropertyDescription(
            'NavigationProperty',
            $this->propertyReflectionFixture,
            $partnerEntityTypeStub
        );

        $ownerEntityType = $this->buildEntityTypeStub();
        $navigationPropertyDescription->setStructuredType($ownerEntityType);

        $otherEntityType = $this->buildEntityTypeStub();

        $partnerNavigationProperty = new NavigationPropertyDescription(
            'PartnerProperty',
            $this->propertyReflectionFixture,
            $otherEntityType
        );

        $partnerNavigationProperty->setStructuredType($partnerEntityTypeStub);
        $navigationPropertyDescription->setPartner($partnerNavigationProperty);
    }

    protected function buildEntityTypeStub()
    {
        $entityTypeStub = $this->getMockBuilder('Rolab\EntityDataModel\Type\EntityType')
            ->disableOriginalConstructor()
            ->getMock();

        $entityTypeStub->method('isSubTypeOf')
            ->will($this->returnCallback(function () use ($entityTypeStub) {
                return func_get_args()[0] === $entityTypeStub;
            }));

        return $entityTypeStub;
    }
}
