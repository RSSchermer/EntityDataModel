<?php

declare(strict_types=1);

namespace RSSchermer\Tests\EntityModel\Type;

use RSSchermer\EntityModel\Exception\InvalidArgumentException;
use RSSchermer\EntityModel\Type\Edm\EdmString;
use RSSchermer\EntityModel\Type\PrimitivePropertyDescription;

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
            'RSSchermer\Tests\EntityModel\Fixtures\Address',
            'street'
        );

        $this->propertyValueTypeFixture = EdmString::create();
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
