<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel\Tests;

use Rolab\EntityDataModel\EntityDataModel;
use Rolab\EntityDataModel\Exception\InvalidArgumentException;

/**
 * @covers EntityDataModel
 */
class EntityDataModelTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $edm = new EntityDataModel('http://www.model.org', 'Fully.Qualified.Namespace');

        $this->assertEquals('http://www.model.org', $edm->getUri());
        $this->assertEquals('Fully.Qualified.Namespace', $edm->getRealNamespace());
        $this->assertNull($edm->getNamespaceAlias());
        $this->assertEquals('Fully.Qualified.Namespace', $edm->getNamespace());

        return $edm;
    }

    /**
     * @dataProvider invalidNamespaceProvider
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnInvalidNamespace(string $invalidNamespace)
    {
        new EntityDataModel('http://www.model.org', $invalidNamespace);
    }

    public function testConstructorWithAlias()
    {
        $edm = new EntityDataModel('http://www.model.org', 'Fully.Qualified.Namespace', 'Self');

        $this->assertEquals('Self', $edm->getNamespace());

        return $edm;
    }

    /**
     * @depends testConstructor
     */
    public function testSetNamespaceAlias(EntityDataModel $edm)
    {
        $edm->setNamespaceAlias('Self');

        $this->assertEquals('Self', $edm->getNamespaceAlias());
    }

    /**
     * @dataProvider invalidNamespaceProvider
     * @depends testConstructor
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnSetInvalidNamespaceAlias(string $invalidAlias, EntityDataModel $edm)
    {
        $edm->setNamespaceAlias($invalidAlias);
    }

    /**
     * @depends testConstructorWithAlias
     */
    public function testGetReferencedModelsInitiallyEmpty(EntityDataModel $edm)
    {
        $this->assertEmpty($edm->getReferencedModels());

        return $edm;
    }

    /**
     * @depends testGetReferencedModelsInitiallyEmpty
     */
    public function testAddReferencedModel(EntityDataModel $edm)
    {
        $referencedModel = new EntityDataModel('ReferencedModelURI', 'Some.Referenced.Model', 'Some.Alias');

        $edm->addReferencedModel($referencedModel, 'Referenced');

        $this->assertCount(1, $edm->getReferencedModels());
        $this->assertContains($referencedModel, $edm->getReferencedModels());
        $this->assertEquals('Referenced', $referencedModel->getNamespace());

        return $edm;
    }

    /**
     * @depends testConstructorWithAlias
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnAddReferencedModelWithSameNamespaceAsModel(EntityDataModel $edm)
    {
        $referencedModel = new EntityDataModel('ReferencedModelURI', 'Fully.Qualified.Namespace');

        $edm->addReferencedModel($referencedModel, 'Self');
    }

    /**
     * @depends testAddReferencedModel
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnAddReferencedModelWithSameNamespaceAsOtherReferencedModel(EntityDataModel $edm)
    {
        $referencedModel = new EntityDataModel('ReferencedModelURI', 'Fully.Qualified.Namespace');

        $edm->addReferencedModel($referencedModel, 'Referenced');
    }

    /**
     * @depends testAddReferencedModel
     */
    public function testGetReferencedModelByName(EntityDataModel $edm)
    {
        $this->assertNull($edm->getReferencedModelByNamespace('Non.Existant.Namespace'));

        $this->assertEquals('Referenced', $edm->getReferencedModelByNamespace('Referenced')->getNamespace());
    }

    /**
     * @depends testConstructorWithAlias
     */
    public function testGetStructuredTypesInitiallyEmpty(EntityDataModel $edm)
    {
        $this->assertEmpty($edm->getStructuredTypes());

        return $edm;
    }

    /**
     * @depends testGetStructuredTypesInitiallyEmpty
     */
    public function testAddStructuredType(EntityDataModel $edm)
    {
        $mockStructuredType = $this->buildStructuredTypeStub('SomeType', 'Some\Class');

        $mockStructuredType->expects($this->once())
            ->method('setEntityDataModel')
            ->with($this->equalTo($edm));

        $edm->addStructuredType($mockStructuredType);

        $this->assertCount(1, $edm->getStructuredTypes());
        $this->assertContains($mockStructuredType, $edm->getStructuredTypes());

        return $edm;
    }

    /**
     * @depends testAddStructuredType
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnAddStructuredTypeWithSameName(EntityDataModel $edm)
    {
        $edm->addStructuredType($this->buildStructuredTypeStub('SomeType', 'Some\Other\Class'));
    }

    /**
     * @depends testAddStructuredType
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnAddStructuredTypeWithSameClassName(EntityDataModel $edm)
    {
        $edm->addStructuredType($this->buildStructuredTypeStub('SomeOtherType', 'Some\Class'));
    }

    /**
     * @depends testAddStructuredType
     *
     * I would actually like this to depend on testGetStructuredTypesInitiallyEmpty and get an empty
     * edm to start this test with. However, it seems that the state of the edm object returned by
     * testGetStructuredTypesInitiallyEmpty gets altered by testAddStructuredType, even if it is not
     * part of the dependency hierarchy of this test, but rather on another branch.
     */
    public function testGetStructuredTypeByName(EntityDataModel $edm)
    {
        $structuredTypeStub = $this->buildStructuredTypeStub('SomeOtherType', 'Some\Other\Class');

        $edm->addStructuredType($structuredTypeStub);

        $this->assertSame($structuredTypeStub, $edm->getStructuredTypeByName('SomeOtherType'));

        return $edm;
    }

    /**
     * @depends testGetStructuredTypeByName
     */
    public function testGetStructuredTypeByClassName(EntityDataModel $edm)
    {
        $structuredTypeStub = $this->buildStructuredTypeStub('SomeThirdType', 'Some\Third\Class');

        $edm->addStructuredType($structuredTypeStub);

        $this->assertSame($structuredTypeStub, $edm->getStructuredTypeByClassName('Some\Third\Class'));
    }

    public function testFindStructuredTypeByFullName()
    {
        $edm = new EntityDataModel('ModelURI', 'Fully.Qualified.Namespace', 'Self');

        $referencedModel = new EntityDataModel('ReferencedModelURI', 'Referenced.Namespace');

        $edm->addReferencedModel($referencedModel, 'Referenced');

        $stucturalTypeStubInMainModel = $this->buildStructuredTypeStub('MainModelType', 'Some\Class');
        $stucturalTypeStubInReferencedModel = $this->buildStructuredTypeStub('ReferencedModelType', 'Some\Other\Class');

        $edm->addStructuredType($stucturalTypeStubInMainModel);
        $referencedModel->addStructuredType($stucturalTypeStubInReferencedModel);

        $this->assertNull($edm->findStructuredTypeByFullName('NonExistantName'));
        $this->assertSame($stucturalTypeStubInMainModel, $edm->findStructuredTypeByFullName('Self.MainModelType'));
        $this->assertSame($stucturalTypeStubInMainModel, $edm->findStructuredTypeByFullName('MainModelType'));
        $this->assertNull($edm->findStructuredTypeByFullName('ReferencedModelType'));
        $this->assertSame(
            $stucturalTypeStubInReferencedModel,
            $edm->findStructuredTypeByFullName('Referenced.ReferencedModelType')
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

    protected function buildStructuredTypeStub($name, $className)
    {
        $structuredTypeStub = $this->getMockBuilder('Rolab\EntityDataModel\Type\StructuredType')
            ->disableOriginalConstructor()
            ->setMethods(array('setEntityDataModel', 'getName', 'getClassName'))
            ->getMockForAbstractClass();

        $structuredTypeStub->method('getName')->willReturn($name);
        $structuredTypeStub->method('getClassName')->willReturn($className);

        return $structuredTypeStub;
    }
}
