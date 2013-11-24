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

use Rolab\EntityDataModel\Type\NavigationPropertyDescription;

class NavigationPropertyDescriptionTest extends EntityDataModelTestCase
{
    protected $propertyReflectionFixture;

    protected $associationFixture;

    protected function setUp()
    {
        $this->propertyReflectionFixture = new \ReflectionProperty('Rolab\EntityDataModel\Tests\Fixtures\Car',
            'kilometersDriven');
    }

    public function testConstructor()
    {
        $associationEndStub = $this->getMockBuilder('Rolab\EntityDataModel\AssociationEnd')
            ->disableOriginalConstructor()
            ->getMock();

        $associationFixture = $this->getMockBuilder('Rolab\EntityDataModel\Association')
            ->disableOriginalConstructor()
            ->setMethods(array('getEndByRole'))
            ->getMock();

        $associationFixture->expects($this->any())
            ->method('getEndByRole')
            ->with($this->logicalOr($this->equalTo('RoleOne'), $this->equalTo('RoleTwo')))
            ->will($this->returnValue($associationEndStub));

        $navigationPropertyDescription = new NavigationPropertyDescription('NavigationProperty',
            $this->propertyReflectionFixture, $associationFixture, 'RoleOne', 'RoleTwo');

        $this->assertSame($associationFixture, $navigationPropertyDescription->getAssocation());
        $this->assertEquals('RoleOne', $navigationPropertyDescription->getFromRole());
        $this->assertEquals('RoleTwo', $navigationPropertyDescription->getToRole());
    }
}
