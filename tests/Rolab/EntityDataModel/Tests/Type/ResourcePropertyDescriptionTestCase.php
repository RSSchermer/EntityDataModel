<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel\Tests\Type;

use Rolab\EntityDataModel\Type\ResourcePropertyDescription;

/**
 * @covers ResourcePropertyDescription
 */
abstract class ResourcePropertyDescriptionTestCase extends \PHPUnit_Framework_TestCase
{
    abstract public function testConstructor();

    /**
     * @depends testConstructor
     */
    public function testSetStructuredType(ResourcePropertyDescription $resourcePropertyDescription)
    {
        $complexTypeStub = $this->getMockBuilder('Rolab\EntityDataModel\Type\ComplexType')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $resourcePropertyDescription->setStructuredType($complexTypeStub);

        $this->assertSame($complexTypeStub, $resourcePropertyDescription->getStructuredType());
    }

    public function invalidNameProvider()
    {
        return array(
            array('A-dashed-name'),
            array('N@meW|thS%mbols'),
            array('Name With Spaces'),
            array('Name.With.Dots')
        );
    }
}
