<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel\Tests\Type;

use Rolab\EntityDataModel\Exception\InvalidArgumentException;
use Rolab\EntityDataModel\Type\PrimitivePropertyDescription;

/**
 * @covers PrimitivePropertyDescription
 */
class PrimitivePropertyDescriptionTest extends ResourcePropertyDescriptionTestCase
{
    protected $propertyReflectionFixture;

    protected $propertyValueTypeFixture;

    protected function setUp()
    {
        $this->propertyReflectionFixture = new \ReflectionProperty(
            'Rolab\EntityDataModel\Tests\Fixtures\Address',
            'street'
        );

        $this->propertyValueTypeFixture = $this->getMockBuilder('Rolab\EntityDataModel\Type\PrimitiveType')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testConstructor()
    {
        $primitivePropertyDescription = new PrimitivePropertyDescription(
            'Street',
            $this->propertyReflectionFixture,
            $this->propertyValueTypeFixture
        );

        $this->assertEquals('Street', $primitivePropertyDescription->getName());
        $this->assertSame($this->propertyReflectionFixture, $primitivePropertyDescription->getReflection());
        $this->assertSame($this->propertyValueTypeFixture, $primitivePropertyDescription->getPropertyValueType());
        $this->assertFalse($primitivePropertyDescription->isCollection());
        $this->assertTrue($primitivePropertyDescription->isNullable());
        $this->assertFalse($primitivePropertyDescription->isPartOfKey());
        $this->assertFalse($primitivePropertyDescription->isPartOfETag());

        return $primitivePropertyDescription;
    }

    public function testConstructorWithIsCollection()
    {
        $primitivePropertyDescription = new PrimitivePropertyDescription(
            'SomeProperty',
            $this->propertyReflectionFixture,
            $this->propertyValueTypeFixture,
            true
        );

        $this->assertTrue($primitivePropertyDescription->isCollection());
    }

    public function testConstructorWithNullable()
    {
        $primitivePropertyDescription = new PrimitivePropertyDescription(
            'SomeProperty',
            $this->propertyReflectionFixture,
            $this->propertyValueTypeFixture,
            false,
            false
        );

        $this->assertFalse($primitivePropertyDescription->isNullable());
    }

    public function testConstructorWithPartOfKey()
    {
        $primitivePropertyDescription = new PrimitivePropertyDescription(
            'SomeProperty',
            $this->propertyReflectionFixture,
            $this->propertyValueTypeFixture,
            false,
            true,
            true
        );

        $this->assertTrue($primitivePropertyDescription->isPartOfKey());
    }

    public function testConstructorWithPartOfETag()
    {
        $primitivePropertyDescription = new PrimitivePropertyDescription(
            'SomeProperty',
            $this->propertyReflectionFixture,
            $this->propertyValueTypeFixture,
            false,
            true,
            false,
            true
        );

        $this->assertTrue($primitivePropertyDescription->isPartOfETag());
    }


    /**
     * @dataProvider invalidNameProvider
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnInvalidName(string $invalidName)
    {
        new PrimitivePropertyDescription(
            $invalidName,
            $this->propertyReflectionFixture,
            $this->propertyValueTypeFixture
        );
    }
}
