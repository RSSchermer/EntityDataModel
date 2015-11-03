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

use Rolab\EntityDataModel\Type\ResourcePropertyDescription;

/**
 * @covers ResourcePropertyDescription
 */
class ResourcePropertyDescriptionTest extends EntityDataModelTestCase
{
    protected $propertyReflectionFixture;

    protected function setUp()
    {
        $this->propertyReflectionFixture = new \ReflectionProperty('Rolab\EntityDataModel\Tests\Fixtures\Car',
            'kilometersDriven');
    }

    public function testConstructor()
    {
        $mockResourcePropertyDescription =
            $this->getMockBuilder('Rolab\EntityDataModel\Type\ResourcePropertyDescription')
            ->setConstructorArgs(array('SomeProperty', $this->propertyReflectionFixture))
            ->getMockForAbstractClass();

        $this->assertEquals('SomeProperty', $mockResourcePropertyDescription->getName());
        $this->assertSame($this->propertyReflectionFixture, $mockResourcePropertyDescription->getReflection());

        return $mockResourcePropertyDescription;
    }

    /**
     * @dataProvider invalidNameProvider
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnInvalidName($invalidName)
    {
        $mockResourcePropertyDescription =
            $this->getMockBuilder('Rolab\EntityDataModel\Type\ResourcePropertyDescription')
            ->setConstructorArgs(array($invalidName, $this->propertyReflectionFixture))
            ->getMockForAbstractClass();
    }

    /**
     * @depends testConstructor
     */
    public function testSetComplexType(ResourcePropertyDescription $resourcePropertyDescription)
    {
        $complexTypeStub = $this->getMockBuilder('Rolab\EntityDataModel\Type\ComplexType')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $resourcePropertyDescription->setStructuredType($complexTypeStub);

        $this->assertSame($complexTypeStub, $resourcePropertyDescription->getStructuredType());
    }
}
