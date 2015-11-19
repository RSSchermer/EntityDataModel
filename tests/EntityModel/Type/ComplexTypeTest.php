<?php

declare(strict_types=1);

namespace RSSchermer\Tests\EntityModel\Type;

use RSSchermer\EntityModel\Exception\InvalidArgumentException;
use RSSchermer\EntityModel\Type\ComplexType;
use RSSchermer\Tests\EntityModel\Stubs\StructuralPropertyDescriptionStub;

/**
 * @covers ComplexType
 */
class ComplexTypeTest extends ComplexTypeTestCase
{
    protected $adressReflectionClassFixture;

    protected function setUp()
    {
        parent::setUp();

        $this->adressReflectionClassFixture = new \ReflectionClass('RSSchermer\Tests\EntityModel\Fixtures\Address');
    }

    public function testConstructor()
    {
        $propertyStub = new StructuralPropertyDescriptionStub();

        $complexType = new ComplexType(
            'Address',
            $this->adressReflectionClassFixture,
            array($propertyStub)
        );

        $this->assertEquals('Address', $complexType->getName());
        $this->assertSame($this->adressReflectionClassFixture, $complexType->getReflection());
        $this->assertEquals('RSSchermer\Tests\EntityModel\Fixtures\Address', $complexType->getClassName());
        $this->assertContains($propertyStub, $complexType->getPropertyDescriptions());

        return $complexType;
    }

    /**
     * @dataProvider invalidNameProvider
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnInvalidName(string $invalidName)
    {
        new ComplexType($invalidName, $this->adressReflectionClassFixture);
    }
}
