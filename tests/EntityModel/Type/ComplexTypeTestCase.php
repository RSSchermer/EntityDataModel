<?php

declare(strict_types=1);

namespace RSSchermer\Tests\EntityModel\Type;

use RSSchermer\Tests\EntityModel\NamedModelElementTestCase;

use RSSchermer\EntityModel\Type\ComplexType;
use RSSchermer\EntityModel\Exception\InvalidArgumentException;
use RSSchermer\Tests\EntityModel\Stubs\StructuralPropertyDescriptionStub;

abstract class ComplexTypeTestCase extends NamedModelElementTestCase
{
    /**
     * @depends testConstructor
     */
    public function testAddStructuralProperty(ComplexType $complexType)
    {
        $propertyCount = $complexType->getPropertyDescriptions()->count();
        $someProperty = new StructuralPropertyDescriptionStub('SomeProperty');

        $complexType->addStructuralPropertyDescription($someProperty);

        $this->assertCount($propertyCount + 1, $complexType->getPropertyDescriptions());
        $this->assertContains($someProperty, $complexType->getPropertyDescriptions());

        return $complexType;
    }

    /**
     * @depends testAddStructuralProperty
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnAddStructuralPropertyWithSameName(ComplexType $complexType)
    {
        $complexType->addStructuralPropertyDescription(new StructuralPropertyDescriptionStub('SomeProperty'));
    }

    /**
     * @depends testAddStructuralProperty
     */
    public function testGetPropertyDescriptionByName(ComplexType $complexType)
    {
        $someProperty = new StructuralPropertyDescriptionStub('SomeOtherProperty');

        $complexType->addStructuralPropertyDescription($someProperty);

        $this->assertSame($someProperty, $complexType->getPropertyDescriptionByName('SomeOtherProperty')->get());
    }
}
