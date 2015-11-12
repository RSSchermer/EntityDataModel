<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel\Tests\Type;

use Rolab\EntityDataModel\Exception\InvalidArgumentException;
use Rolab\EntityDataModel\Type\ComplexType;

/**
 * @covers ComplexType
 */
class ComplexTypeTest extends ComplexTypeTestCase
{
    protected $adressReflectionClassFixture;

    protected function setUp()
    {
        parent::setUp();

        $this->adressReflectionClassFixture = new \ReflectionClass('Rolab\EntityDataModel\Tests\Fixtures\Address');
    }

    public function testConstructor()
    {
        $propertyStub = $this->buildStructuralPropertyDescriptionStub('City');

        $complexType = new ComplexType('Address', $this->adressReflectionClassFixture,
            array($propertyStub));

        $this->assertEquals('Address', $complexType->getName());
        $this->assertSame($this->adressReflectionClassFixture, $complexType->getReflection());
        $this->assertEquals('Rolab\EntityDataModel\Tests\Fixtures\Address', $complexType->getClassName());
        $this->assertContains($propertyStub, $complexType->getPropertyDescriptions());

        return $complexType;
    }

    /**
     * @dataProvider invalidNameProvider
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnInvalidName(string $invalidName)
    {
        $propertyStub = $this->buildStructuralPropertyDescriptionStub('City');

        new ComplexType($invalidName, $this->adressReflectionClassFixture);
    }
}
