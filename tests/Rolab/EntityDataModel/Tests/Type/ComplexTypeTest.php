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

use Rolab\EntityDataModel\Type\ComplexType;

/**
 * @covers ComplexType
 */
class ComplexTypeTest extends EntityDataModelTestCase
{
    protected $adressReflectionClassFixture;

    protected function setUp()
    {
        parent::setUp();

        $this->adressReflectionClassFixture = new \ReflectionClass('Rolab\EntityDataModel\Tests\Fixtures\Adress');
    }

    public function testConstructor()
    {
        $propertyStub = $this->buildStructuralPropertyDescriptionStub('City');

        $complexType = new ComplexType('AdressType', $this->adressReflectionClassFixture,
            array($propertyStub));

        $this->assertEquals('AdressType', $complexType->getName());
        $this->assertSame($this->adressReflectionClassFixture, $complexType->getReflection());
        $this->assertEquals('Rolab\EntityDataModel\Tests\Fixtures\Adress', $complexType->getClassName());
        $this->assertContains($propertyStub, $complexType->getPropertyDescriptions());

        return $complexType;
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnEmptyPropertyDescriptionArray()
    {
        $complexType = new ComplexType('AdressType', $this->adressReflectionClassFixture, array());
    }

    /**
     * @depends testConstructor
     */
    public function testAddProperty(ComplexType $complexType)
    {
        $this->assertCount(1, $complexType->getPropertyDescriptions());

        $propertyStub = $this->buildStructuralPropertyDescriptionStub('Street');

        $complexType->addPropertyDescription($propertyStub);

        $this->assertCount(2, $complexType->getPropertyDescriptions());
        $this->assertContains($propertyStub, $complexType->getPropertyDescriptions());

        return $complexType;
    }

    /**
     * @depends testAddProperty
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnAddPropertyWithSameName(ComplexType $complexType)
    {
        $complexType->addPropertyDescription($this->buildStructuralPropertyDescriptionStub('Street'));
    }

    /**
     * @depends testAddProperty
     */
    public function testRemovePropertyDescription(ComplexType $complexType)
    {
        $someProperty = $this->buildStructuralPropertyDescriptionStub('HouseNumber');

        $complexType->addPropertyDescription($someProperty);

        $this->assertCount(3, $complexType->getPropertyDescriptions());
        $this->assertContains($someProperty, $complexType->getPropertyDescriptions());

        $complexType->removePropertyDescription('HouseNumber');

        $this->assertCount(2, $complexType->getPropertyDescriptions());
        $this->assertNotContains($someProperty, $complexType->getPropertyDescriptions());
    }

    /**
     * @expectedException RuntimeException
     */
    public function testExceptionOnRemoveLastPropertyDescription()
    {
        $complexType = new ComplexType('AdressType', $this->adressReflectionClassFixture,
            array($this->buildStructuralPropertyDescriptionStub('City')));

        $complexType->removePropertyDescription('City');
    }

    /**
     * @depends testAddProperty
     */
    public function testGetPropertyDescriptionByName(ComplexType $complexType)
    {
        $someProperty = $this->buildStructuralPropertyDescriptionStub('HouseNumber');

        $complexType->addPropertyDescription($someProperty);

        $this->assertSame($someProperty, $complexType->getPropertyDescriptionByName('HouseNumber'));
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
