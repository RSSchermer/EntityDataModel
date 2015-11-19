<?php

declare(strict_types=1);

namespace RSSchermer\Tests\EntityModel\Type;

use RSSchermer\EntityModel\Exception\InvalidArgumentException;
use RSSchermer\EntityModel\Type\ComplexPropertyDescription;
use RSSchermer\EntityModel\Type\ComplexType;

/**
 * @covers ComplexPropertyDescription
 */
class ComplexPropertyDescriptionTest extends ResourcePropertyDescriptionTestCase
{
    protected $propertyReflectionFixture;

    protected $propertyValueTypeFixture;

    protected function setUp()
    {
        $this->propertyReflectionFixture = new \ReflectionProperty(
            'RSSchermer\Tests\EntityModel\Fixtures\Customer',
            'address'
        );

        $this->propertyValueTypeFixture = new ComplexType(
            'Address',
            new \ReflectionClass('RSSchermer\Tests\EntityModel\Fixtures\Address')
        );
    }

    public function testConstructor()
    {
        $complexPropertyDescription = new ComplexPropertyDescription(
            'Address',
            $this->propertyReflectionFixture,
            $this->propertyValueTypeFixture
        );

        $this->assertEquals('Address', $complexPropertyDescription->getName());
        $this->assertSame($this->propertyReflectionFixture, $complexPropertyDescription->getReflection());
        $this->assertSame($this->propertyValueTypeFixture, $complexPropertyDescription->getPropertyValueType());
        $this->assertFalse($complexPropertyDescription->isCollection());

        return $complexPropertyDescription;
    }

    public function testConstructorWithIsCollection()
    {
        $complexPropertyDescription = new ComplexPropertyDescription(
            'Address',
            $this->propertyReflectionFixture,
            $this->propertyValueTypeFixture,
            true
        );

        $this->assertTrue($complexPropertyDescription->isCollection());
    }

    /**
     * @dataProvider invalidNameProvider
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnInvalidName(string $invalidName)
    {
        new ComplexPropertyDescription(
            $invalidName,
            $this->propertyReflectionFixture,
            $this->propertyValueTypeFixture
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnEntityTypeAsPropertyValueType()
    {
        $entityTypeStub = $this->getMockBuilder('RSSchermer\EntityModel\Type\EntityType')
            ->disableOriginalConstructor()
            ->getMock();

        new ComplexPropertyDescription(
            'Address',
            $this->propertyReflectionFixture,
            $entityTypeStub
        );
    }
}
