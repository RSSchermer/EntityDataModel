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

use Rolab\EntityDataModel\Type\StructuredType;

/**
 * @covers StructuralType
 */
class StructuralTypeTest extends EntityDataModelTestCase
{
    protected $personReflectionClassFixture;

    protected function setUp()
    {
        $this->personReflectionClassFixture = new \ReflectionClass('Rolab\EntityDataModel\Tests\Fixtures\Person');
    }

    public function testConstructor()
    {
        $mockStructuralType = $this->getMockBuilder('Rolab\EntityDataModel\Type\StructuralType')
            ->setConstructorArgs(array('PersonType', $this->personReflectionClassFixture))
            ->getMockForAbstractClass();

        $this->assertEquals('PersonType', $mockStructuralType->getName());
        $this->assertSame($this->personReflectionClassFixture, $mockStructuralType->getReflection());
        $this->assertEquals('Rolab\EntityDataModel\Tests\Fixtures\Person', $mockStructuralType->getClassName());

        return $mockStructuralType;
    }

    /**
     * @dataProvider invalidNameProvider
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnInvalidName($invalidName)
    {
        $mockStructuralType = $this->getMockBuilder('Rolab\EntityDataModel\Type\StructuralType')
            ->setConstructorArgs(array($invalidName, $this->personReflectionClassFixture))
            ->getMockForAbstractClass();
    }

    /**
     * @depends testConstructor
     */
    public function testSetEntityDataModel(StructuredType $structuralType)
    {
        $entityDataModelStub = $this->getMockBuilder('Rolab\EntityDataModel\EntityDataModel')
            ->disableOriginalConstructor()
            ->getMock();

        $entityDataModelStub->expects($this->any())
            ->method('getNamespace')
            ->will($this->returnValue('SomeNamespace'));

        $structuralType->setEntityDataModel($entityDataModelStub);

        $this->assertSame($entityDataModelStub, $structuralType->getEntityDataModel());
        $this->assertEquals('SomeNamespace', $structuralType->getNamespace());
        $this->assertEquals('SomeNamespace.PersonType', $structuralType->getFullName());
    }
}
