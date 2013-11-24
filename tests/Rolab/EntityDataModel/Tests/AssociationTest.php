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

use Rolab\EntityDataModel\Association;
use Rolab\EntityDataModel\AssociationEnd;

/**
 * @covers Association
 * @covers AssociationEnd
 */
class AssociationTest extends EntityDataModelTestCase
{
    protected $endOneFixture;

    protected $endTwoFixture;

    protected function setUp()
    {
        $someEntityTypeStub = $this->buildEntityTypeStub('SomeName', 'Some\Class');
        $someOtherEntityTypeStub = $this->buildEntityTypeStub('SomeOtherName', 'Some\Other\Class');

        $this->endOneFixture = new AssociationEnd('RoleOne', $someEntityTypeStub);
        $this->endTwoFixture = new AssociationEnd('RoleTwo', $someOtherEntityTypeStub);
    }

    public function testConstructor()
    {
        $association = new Association('SomeName', $this->endOneFixture, $this->endTwoFixture);

        $this->assertEquals('SomeName', $association->getName());
        $this->assertContains($this->endOneFixture, $association->getEnds());
        $this->assertContains($this->endTwoFixture, $association->getEnds());
        $this->assertEquals(Association::DELETE_ACTION_NONE, $association->getOnDeleteAction());

        return $association;
    }

    public function testConstructorWithOnDeleteCascadeAction()
    {
        $association = new Association('SomeName', $this->endOneFixture, $this->endTwoFixture,
            Association::DELETE_ACTION_CASCADE);

        $this->assertEquals(Association::DELETE_ACTION_CASCADE, $association->getOnDeleteAction());
    }

    /**
     * @dataProvider invalidNameProvider
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnInvalidName($invalidName)
    {
        $someAssociation = new Association($invalidName, $this->endOneFixture, $this->endTwoFixture);
    }

    /**
     * @depends testConstructor
     */
    public function testSetEntityDataModel(Association $association)
    {
        $entityDataModelStub = $this->getMockBuilder('Rolab\EntityDataModel\EntityDataModel')
            ->disableOriginalConstructor()
            ->getMock();

        $entityDataModelStub->expects($this->any())
            ->method('getNamespace')
            ->will($this->returnValue('SomeNamespace'));

        $association->setEntityDataModel($entityDataModelStub);

        $this->assertSame($entityDataModelStub, $association->getEntityDataModel());
        $this->assertEquals('SomeNamespace.SomeName', $association->getFullName());
    }

    /**
     * @depends testConstructor
     */
    public function testGetEndByRole(Association $association)
    {
        $this->assertEquals($this->endOneFixture, $association->getEndByRole('RoleOne'));
        $this->assertEquals($this->endTwoFixture, $association->getEndByRole('RoleTwo'));
    }

    protected function buildEntityTypeStub()
    {
        $entityTypeStub = $this->getMockBuilder('Rolab\EntityDataModel\Type\EntityType')
            ->disableOriginalConstructor()
            ->getMock();

        return $entityTypeStub;
    }
}
