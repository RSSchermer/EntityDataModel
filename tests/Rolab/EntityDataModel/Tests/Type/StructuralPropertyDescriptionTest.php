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

use Rolab\EntityDataModel\Type\StructuralPropertyDescription;

class StructuralPropertyDescriptionTest extends EntityDataModelTestCase
{
    protected $propertyReflectionFixture;

    protected $propertyValueTypeFixture;

    protected function setUp()
    {
        $this->propertyReflectionFixture = new \ReflectionProperty('Rolab\EntityDataModel\Tests\Fixtures\Car',
            'kilometersDriven');

        $this->propertyValueTypeFixture = $this->getMockBuilder('Rolab\EntityDataModel\Type\ResourceType')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testConstructor()
    {
        $mockStructuralPropertyDescription =
            $this->getMockBuilder('Rolab\EntityDataModel\Type\StructuralPropertyDescription')
            ->setConstructorArgs(array('KilometersDriven', $this->propertyReflectionFixture,
                $this->propertyValueTypeFixture))
            ->getMockForAbstractClass();

        $this->assertEquals('KilometersDriven', $mockStructuralPropertyDescription->getName());
        $this->assertSame($this->propertyReflectionFixture, $mockStructuralPropertyDescription->getReflection());
        $this->assertSame($this->propertyValueTypeFixture, $mockStructuralPropertyDescription->getPropertyValueType());
        $this->assertFalse($mockStructuralPropertyDescription->isCollection());

        return $mockStructuralPropertyDescription;
    }

    public function testConstructorWithIsCollection()
    {
        $mockStructuralPropertyDescription =
            $this->getMockBuilder('Rolab\EntityDataModel\Type\StructuralPropertyDescription')
            ->setConstructorArgs(array('KilometersDriven', $this->propertyReflectionFixture,
                $this->propertyValueTypeFixture, true))
            ->getMockForAbstractClass();

        $this->assertTrue($mockStructuralPropertyDescription->isCollection());
    }

    /**
     * @depends testConstructor
     */
    public function testIsNullableInitiallyTrue(StructuralPropertyDescription $structuralPropertyDescription)
    {
        $this->assertTrue($structuralPropertyDescription->isNullable());

        return $structuralPropertyDescription;
    }

    /**
     * @depends testIsNullableInitiallyTrue
     */
    public function testSetNullable(StructuralPropertyDescription $structuralPropertyDescription)
    {
        $structuralPropertyDescription->setNullable(false);

        $this->assertFalse($structuralPropertyDescription->isNullable());
    }
}
