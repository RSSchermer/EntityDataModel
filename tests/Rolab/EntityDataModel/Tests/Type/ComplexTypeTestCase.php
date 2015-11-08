<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel\Tests\Type;

use Rolab\EntityDataModel\Tests\NamedModelElementTestCase;

use Rolab\EntityDataModel\Type\ComplexType;
use Rolab\EntityDataModel\Exception\InvalidArgumentException;

abstract class ComplexTypeTestCase extends NamedModelElementTestCase
{    
    /**
     * @depends testConstructor
     */
    public function testAddStructuralProperty(ComplexType $complexType)
    {
        $propertyCount = count($complexType->getPropertyDescriptions());
        $someProperty = $this->buildStructuralPropertyDescriptionStub('SomeProperty');

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
        $complexType->addStructuralPropertyDescription($this->buildStructuralPropertyDescriptionStub('SomeProperty'));
    }

    /**
     * @depends testAddStructuralProperty
     */
    public function testGetPropertyDescriptionByName(ComplexType $complexType)
    {
        $someProperty = $this->buildStructuralPropertyDescriptionStub('SomeOtherProperty');

        $complexType->addStructuralPropertyDescription($someProperty);

        $this->assertSame($someProperty, $complexType->getPropertyDescriptionByName('SomeOtherProperty'));
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

        $propertyDescriptionStub->method('getName')->willReturn($name);

        return $propertyDescriptionStub;
    }
}
