<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel\Tests\Type;

use Rolab\EntityDataModel\Exception\InvalidArgumentException;
use Rolab\EntityDataModel\Type\ComplexPropertyDescription;

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
            'Rolab\EntityDataModel\Tests\Fixtures\Customer',
            'address'
        );

        $this->propertyValueTypeFixture = $this->getMockBuilder('Rolab\EntityDataModel\Type\ComplexType')
            ->disableOriginalConstructor()
            ->getMock();
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
        $entityTypeStub = $this->getMockBuilder('Rolab\EntityDataModel\Type\EntityType')
            ->disableOriginalConstructor()
            ->getMock();

        new ComplexPropertyDescription(
            'Address',
            $this->propertyReflectionFixture,
            $entityTypeStub
        );
    }
}
