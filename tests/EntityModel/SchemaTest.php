<?php

declare(strict_types=1);

namespace RSSchermer\Tests\EntityModel;

use RSSchermer\EntityModel\Schema;
use RSSchermer\EntityModel\Exception\InvalidArgumentException;
use RSSchermer\Tests\EntityModel\Stubs\StructuredTypeStub;

/**
 * @covers EntityDataModel
 */
class SchemaTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $schema = new Schema('Fully.Qualified.Namespace');

        $this->assertEquals('Fully.Qualified.Namespace', $schema->getNamespace());
        $this->assertFalse($schema->getNamespaceAlias()->isDefined());
        $this->assertEquals('Fully.Qualified.Namespace', $schema->getNamespace());

        return $schema;
    }

    /**
     * @dataProvider invalidNamespaceProvider
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnInvalidNamespace(string $invalidNamespace)
    {
        new Schema($invalidNamespace);
    }

    public function testConstructorWithAlias()
    {
        $schema = new Schema('Fully.Qualified.Namespace', 'Self');

        $this->assertEquals('Self', $schema->getNamespace());

        return $schema;
    }

    /**
     * @depends testConstructor
     */
    public function testSetNamespaceAlias(Schema $schema)
    {
        $schema->setNamespaceAlias('Self');

        $this->assertEquals('Self', $schema->getNamespaceAlias()->get());
    }

    /**
     * @dataProvider invalidNamespaceProvider
     * @depends testConstructor
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnSetInvalidNamespaceAlias(string $invalidAlias, Schema $schema)
    {
        $schema->setNamespaceAlias($invalidAlias);
    }

    /**
     * @depends testConstructorWithAlias
     */
    public function testGetReferencedModelsInitiallyEmpty(Schema $schema)
    {
        $this->assertEmpty($schema->getReferencedSchemas());

        return $schema;
    }

    /**
     * @depends testGetReferencedModelsInitiallyEmpty
     */
    public function testAddReferencedModel(Schema $schema)
    {
        $referencedModel = new Schema('Some.Referenced.Model', 'Some.Alias');

        $schema->addReferencedSchema($referencedModel, 'Referenced');

        $this->assertCount(1, $schema->getReferencedSchemas());
        $this->assertContains($referencedModel, $schema->getReferencedSchemas());

        return $schema;
    }

    /**
     * @depends testConstructorWithAlias
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnAddReferencedModelWithSameNamespaceAsModel(Schema $schema)
    {
        $referencedModel = new Schema('Fully.Qualified.Namespace');

        $schema->addReferencedSchema($referencedModel, 'Self');
    }

    /**
     * @depends testAddReferencedModel
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnAddReferencedModelWithSameNamespaceAsOtherReferencedModel(Schema $schema)
    {
        $referencedModel = new Schema('Fully.Qualified.Namespace');

        $schema->addReferencedSchema($referencedModel, 'Referenced');
    }

    /**
     * @depends testAddReferencedModel
     */
    public function testGetReferencedModelByNamespace(Schema $schema)
    {
        $this->assertFalse($schema->getReferencedSchemaByNamespace('Non.Existent.Namespace')->isDefined());

        $this->assertEquals('Some.Alias', $schema->getReferencedSchemaByNamespace('Referenced')->get()->getNamespace());
    }

    /**
     * @depends testConstructorWithAlias
     */
    public function testGetStructuredTypesInitiallyEmpty(Schema $schema)
    {
        $this->assertEmpty($schema->getStructuredTypes());

        return $schema;
    }

    /**
     * @depends testGetStructuredTypesInitiallyEmpty
     */
    public function testAddStructuredType(Schema $schema)
    {
        $structuredTypeStub = new StructuredTypeStub('Address', 'RSSchermer\Tests\EntityModel\Fixtures\Address');

        $schema->addStructuredType($structuredTypeStub);

        $this->assertCount(1, $schema->getStructuredTypes());
        $this->assertContains($structuredTypeStub, $schema->getStructuredTypes());
        $this->assertSame($schema, $structuredTypeStub->getSchema()->get());

        return $schema;
    }

    /**
     * @depends testAddStructuredType
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnAddStructuredTypeWithSameName(Schema $schema)
    {
        $schema->addStructuredType(new StructuredTypeStub(
            'Address',
            'RSSchermer\Tests\EntityModel\Fixtures\Customer'
        ));
    }

    /**
     * @depends testAddStructuredType
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnAddStructuredTypeWithSameClassName(Schema $schema)
    {
        $schema->addStructuredType(new StructuredTypeStub(
            'SomeOtherType',
            'RSSchermer\Tests\EntityModel\Fixtures\Address'
        ));
    }

    /**
     * @depends testAddStructuredType
     *
     * I would actually like this to depend on testGetStructuredTypesInitiallyEmpty and get an empty
     * edm to start this test with. However, it seems that the state of the edm object returned by
     * testGetStructuredTypesInitiallyEmpty gets altered by testAddStructuredType, even if it is not
     * part of the dependency hierarchy of this test, but rather on another branch.
     */
    public function testGetStructuredTypeByName(Schema $schema)
    {
        $structuredTypeStub = new StructuredTypeStub('Customer', 'RSSchermer\Tests\EntityModel\Fixtures\Customer');

        $schema->addStructuredType($structuredTypeStub);

        $this->assertSame($structuredTypeStub, $schema->getStructuredTypeByName('Customer')->get());

        return $schema;
    }

    /**
     * @depends testGetStructuredTypeByName
     */
    public function testGetStructuredTypeByClassName(Schema $schema)
    {
        $structuredTypeStub = new StructuredTypeStub('Order', 'RSSchermer\Tests\EntityModel\Fixtures\Order');

        $schema->addStructuredType($structuredTypeStub);

        $this->assertSame(
            $structuredTypeStub,
            $schema->getStructuredTypeByClassName('RSSchermer\Tests\EntityModel\Fixtures\Order')->get()
        );
    }

    public function testFindStructuredTypeByFullName()
    {
        $mainModel = new Schema('Fully.Qualified.Namespace', 'Self');

        $referencedModel = new Schema('Referenced.Namespace');

        $mainModel->addReferencedSchema($referencedModel, 'Referenced');

        $structuredTypeStubInMainModel =
            new StructuredTypeStub('Customer', 'RSSchermer\Tests\EntityModel\Fixtures\Customer');
        $structuredTypeStubInReferencedModel =
            new StructuredTypeStub('Order', 'RSSchermer\Tests\EntityModel\Fixtures\Order');

        $mainModel->addStructuredType($structuredTypeStubInMainModel);
        $referencedModel->addStructuredType($structuredTypeStubInReferencedModel);

        $this->assertFalse($mainModel->findStructuredTypeByFullName('NonExistentName')->isDefined());
        $this->assertSame(
            $structuredTypeStubInMainModel,
            $mainModel->findStructuredTypeByFullName('Self.Customer')->get()
        );
        $this->assertSame($structuredTypeStubInMainModel, $mainModel->findStructuredTypeByFullName('Customer')->get());

        $this->assertFalse($mainModel->findStructuredTypeByFullName('Order')->isDefined());
        $this->assertSame(
            $structuredTypeStubInReferencedModel,
            $mainModel->findStructuredTypeByFullName('Referenced.Order')->get()
        );
    }

    public function invalidNamespaceProvider()
    {
        return array(
            array('A-dashed-name'),
            array('NameWithS%mbol'),
            array('Name With Spaces')
        );
    }
}
