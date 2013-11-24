<?php

/*
 * This file is part of the Rolab Entity Data Model library.
 *
 * (c) Roland Schermer <roland0507@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rolab\EntityDataModel\Tests;

use Rolab\EntityDataModel\Tests\EntityDataModelTestCase;

use Rolab\EntityDataModel\AssociationSet;
use Rolab\EntityDataModel\AssociationSetEnd;

/**
 * @covers AssociationSet
 * @covers AssociationSetEnd
 */
class AssociationSetTest extends EntityDataModelTestCase
{
    protected $associationStub;

    protected $associationSetEndOneStub;

    protected $associationSetEndTwoStub;

    protected function setUp()
    {
        $this->associationStub = $this->getMockBuilder('Rolab\EntityDataModel\Association')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testConstructor()
    {
        $associationSet = new AssociationSet('SomeAssociationSet', $this->associationStub);

        $this->assertEquals('SomeAssociationSet', $associationSet->getName());
        $this->assertSame($this->associationStub, $associationSet->getAssociation());
        $this->assertEmpty($associationSet->getSetEnds());

        return $associationSet;
    }

    public function testConstructorWithAssociationSetEnds()
    {
        $this->associationSetEndOneStub = new AssociationSetEnd($this->buildAssociationEndStub('RoleOne'),
            $this->buildEntitySetStub());

        $this->associationSetEndTwoStub = new AssociationSetEnd($this->buildAssociationEndStub('RoleTwo'),
            $this->buildEntitySetStub());

        $associationSet = new AssociationSet('SomeAssociationSet', $this->associationStub,
            $this->associationSetEndOneStub, $this->associationSetEndTwoStub);

        $this->assertContains($this->associationSetEndOneStub, $associationSet->getSetEnds());
        $this->assertContains($this->associationSetEndTwoStub, $associationSet->getSetEnds());

        return $associationSet;
    }

    /**
     * @dataProvider invalidNameProvider
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnInvalidName($invalidName)
    {
        new AssociationSet($invalidName, $this->associationStub);
    }

    /**
     * @depends testConstructor
     */
    public function testSetEntityContainer(AssociationSet $associationSet)
    {
        $entityContainerStub = $this->getMockBuilder('Rolab\EntityDataModel\EntityContainer')
            ->disableOriginalConstructor()
            ->getMock();

        $associationSet->setEntityContainer($entityContainerStub);

        $this->assertSame($entityContainerStub, $associationSet->getEntityContainer());
    }

    /**
     * @depends testConstructorWithAssociationSetEnds
     */
    public function testGetSetEndByRole(AssociationSet $associationSet)
    {
        $this->assertSame($this->associationSetEndOneStub, $associationSet->getSetEndByRole('RoleOne'));
    }

    protected function buildAssociationEndStub($role)
    {
        $associationEndStub = $this->getMockBuilder('Rolab\EntityDataModel\AssociationEnd')
            ->disableOriginalConstructor()
            ->setMethods(array('getRole'))
            ->getMock();

        $associationEndStub->expects($this->any())
            ->method('getRole')
            ->will($this->returnValue($role));

        return $associationEndStub;
    }

    protected function buildEntitySetStub()
    {
        $entitySetStub = $this->getMockBuilder('Rolab\EntityDataModel\EntitySet')
            ->disableOriginalConstructor()
            ->getMock();

        return $entitySetStub;
    }
}
