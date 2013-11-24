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

use Rolab\EntityDataModel\EntityDataModel;

/**
 * @covers EntityDataModel
 */
class EntityDataModelTest extends EntityDataModelTestCase
{
    public function testConstructor()
    {
        $edm = new EntityDataModel('http://some.url.com', 'Fully.Qualified.Namespace');

        $this->assertEquals('http://some.url.com', $edm->getUrl());
        $this->assertEquals('Fully.Qualified.Namespace', $edm->getRealNamespace());
        $this->assertNull($edm->getNamespaceAlias());
        $this->assertEquals('Fully.Qualified.Namespace', $edm->getNamespace());

        return $edm;
    }

    /**
     * @dataProvider invalidNamespaceProvider
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnInvalidNamespace($invalidNamespace)
    {
        new EntityDataModel('http://some.url.com', $invalidNamespace);
    }

    public function testConstructorWithAlias()
    {
        $edm = new EntityDataModel('http://some.url.com', 'Fully.Qualified.Namespace', 'Self');

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
    public function testExceptionOnSetInvalidNamespaceAlias($invalidAlias, EntityDataModel $edm)
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
        $referencedModel = new EntityDataModel('http://referenced.url.com', 'Some.Referenced.Model', 'Some.Alias');

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
        $referencedModel = new EntityDataModel('http://referenced.url.com', 'Fully.Qualified.Namespace');

        $edm->addReferencedModel($referencedModel, 'Self');
    }

    /**
     * @depends testAddReferencedModel
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnAddReferencedModelWithSameNamespaceAsOtherReferencedModel(EntityDataModel $edm)
    {
        $referencedModel = new EntityDataModel('http://referenced.url.com', 'Fully.Qualified.Namespace');

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
    public function testGetStructuralTypesInitiallyEmpty(EntityDataModel $edm)
    {
        $this->assertEmpty($edm->getStructuralTypes());

        return $edm;
    }

    /**
     * @depends testGetStructuralTypesInitiallyEmpty
     */
    public function testAddStructuralType(EntityDataModel $edm)
    {
        $mockStructuralType = $this->buildStructuralTypeStub('SomeType', 'Some\Class');

        $mockStructuralType->expects($this->once())
            ->method('setEntityDataModel')
            ->with($this->equalTo($edm));

        $edm->addStructuralType($mockStructuralType);

        $this->assertCount(1, $edm->getStructuralTypes());
        $this->assertContains($mockStructuralType, $edm->getStructuralTypes());

        return $edm;
    }

    /**
     * @depends testAddStructuralType
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnAddStructuralTypeWithSameName(EntityDataModel $edm)
    {
        $edm->addStructuralType($this->buildStructuralTypeStub('SomeType', 'Some\Other\Class'));
    }

    /**
     * @depends testAddStructuralType
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnAddStructuralTypeWithSameClassName(EntityDataModel $edm)
    {
        $edm->addStructuralType($this->buildStructuralTypeStub('SomeOtherType', 'Some\Class'));
    }

    /**
     * @depends testAddStructuralType
     *
     * I would actually like this to depend on testGetStructuralTypesInitiallyEmpty and get an empty
     * edm to start this test with. However, it seems that the state of the edm object returned by
     * testGetStructuralTypesInitiallyEmpty gets altered by testAddStructuralType, even if it is not
     * part of the dependency hierarchy of this test, but rather on another branch.
     */
    public function testGetStructuralTypeByName(EntityDataModel $edm)
    {
        $stucturalTypeStub = $this->buildStructuralTypeStub('SomeOtherType', 'Some\Other\Class');

        $edm->addStructuralType($stucturalTypeStub);

        $this->assertSame($stucturalTypeStub, $edm->getStructuralTypeByName('SomeOtherType'));

        return $edm;
    }

    /**
     * @depends testGetStructuralTypeByName
     */
    public function testGetStructuralTypeByClassName(EntityDataModel $edm)
    {
        $stucturalTypeStub = $this->buildStructuralTypeStub('SomeThirdType', 'Some\Third\Class');

        $edm->addStructuralType($stucturalTypeStub);

        $this->assertSame($stucturalTypeStub, $edm->getStructuralTypeByClassName('Some\Third\Class'));
    }

    /**
     * @depends testConstructorWithAlias
     */
    public function testGetAssociationsInitiallyEmpty(EntityDataModel $edm)
    {
        $this->assertEmpty($edm->getAssociations());

        return $edm;
    }

    /**
     * @depends testGetAssociationsInitiallyEmpty
     */
    public function testAddAssociation(EntityDataModel $edm)
    {
        $mockAssociation = $this->buildAssociationStub('SomeAssociation');

        $mockAssociation->expects($this->once())
            ->method('setEntityDataModel')
            ->with($this->equalTo($edm));

        $edm->addAssociation($mockAssociation);

        $this->assertCount(1, $edm->getAssociations());
        $this->assertContains($mockAssociation, $edm->getAssociations());

        return $edm;
    }

    /**
     * @depends testAddAssociation
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnAddAssociationWithSameName(EntityDataModel $edm)
    {
        $edm->addAssociation($this->buildAssociationStub('SomeAssociation'));
    }

    /**
     * @depends testAddAssociation
     */
    public function testGetAssociationByName(EntityDataModel $edm)
    {
        $associationStub = $this->buildAssociationStub('SomeOtherAssociation');

        $edm->addAssociation($associationStub);

        $this->assertSame($associationStub, $edm->getAssociationByName('SomeOtherAssociation'));
    }

    /**
     * @depends testConstructorWithAlias
     */
    public function testGetEntityContainersInitiallyEmpty(EntityDataModel $edm)
    {
        $this->assertEmpty($edm->getEntityContainers());

        return $edm;
    }

    /**
     * @depends testGetEntityContainersInitiallyEmpty
     */
    public function testAddEntityContainer(EntityDataModel $edm)
    {
        $mockEntityContainer = $this->buildEntityContainerStub('SomeEntityContainer');

        $mockEntityContainer->expects($this->once())
            ->method('setEntityDataModel')
            ->with($this->equalTo($edm));

        $edm->addEntityContainer($mockEntityContainer);

        $this->assertCount(1, $edm->getEntityContainers());
        $this->assertContains($mockEntityContainer, $edm->getEntityContainers());

        return $edm;
    }

    /**
     * @depends testAddEntityContainer
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnAddEntityContainerWithSameName(EntityDataModel $edm)
    {
        $edm->addEntityContainer($this->buildEntityContainerStub('SomeEntityContainer'));
    }

    /**
     * @depends testAddEntityContainer
     */
    public function testGetEntityContainerByName(EntityDataModel $edm)
    {
        $entityContainerStub = $this->buildEntityContainerStub('SomeOtherEntityContainer');

        $edm->addEntityContainer($entityContainerStub);

        $this->assertSame($entityContainerStub, $edm->getEntityContainerByName('SomeOtherEntityContainer'));
    }

    /**
     * @depends testConstructor
     */
    public function testGetDefaultEntityContainerInitiallyNull(EntityDataModel $edm)
    {
        $this->assertNull($edm->getDefaultEntityContainer());

        return $edm;
    }

    /**
     * @depends testGetDefaultEntityContainerInitiallyNull
     */
    public function testSetDefaultEntityContainer(EntityDataModel $edm)
    {
        $entityContainerStub = $this->buildEntityContainerStub('SomeEntityContainer');
        $otherEntityContainerStub = $this->buildEntityContainerStub('SomeOtherEntityContainer');

        $edm->addEntityContainer($entityContainerStub);
        $edm->addEntityContainer($otherEntityContainerStub);

        $this->assertSame($entityContainerStub, $edm->getDefaultEntityContainer());

        $edm->setDefaultEntityContainer('SomeOtherEntityContainer');

        $this->assertSame($otherEntityContainerStub, $edm->getDefaultEntityContainer());

        return $edm;
    }

    /**
     * @depends testSetDefaultEntityContainer
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnSetDefaultEntityContainerWithNonExistantName(EntityDataModel $edm)
    {
        $edm->setDefaultEntityContainer('NonExistantName');
    }

    public function testFindStructuralTypeByFullName()
    {
        $edm = new EntityDataModel('http://some.url.com', 'Fully.Qualified.Namespace', 'Self');

        $referencedModel = new EntityDataModel('http://referenced.url.com', 'Referenced.Namespace');

        $edm->addReferencedModel($referencedModel, 'Referenced');

        $stucturalTypeStubInMainModel = $this->buildStructuralTypeStub('MainModelType', 'Some\Class');
        $stucturalTypeStubInReferencedModel = $this->buildStructuralTypeStub('ReferencedModelType', 'Some\Other\Class');

        $edm->addStructuralType($stucturalTypeStubInMainModel);
        $referencedModel->addStructuralType($stucturalTypeStubInReferencedModel);

        $this->assertNull($edm->findStructuralTypeByFullName('NonExistantName'));
        $this->assertSame($stucturalTypeStubInMainModel, $edm->findStructuralTypeByFullName(
            'Self.MainModelType'));
        $this->assertSame($stucturalTypeStubInMainModel, $edm->findStructuralTypeByFullName(
            'MainModelType'));
        $this->assertNull($edm->findAssociationByFullName('ReferencedModelType'));
        $this->assertSame($stucturalTypeStubInReferencedModel, $edm->findStructuralTypeByFullName(
            'Referenced.ReferencedModelType'));
    }

    public function testFindAssociationByFullName()
    {
        $edm = new EntityDataModel('http://some.url.com', 'Fully.Qualified.Namespace', 'Self');

        $referencedModel = new EntityDataModel('http://referenced.url.com', 'Referenced.Namespace');

        $edm->addReferencedModel($referencedModel, 'Referenced');

        $associationStubInMainModel = $this->buildAssociationStub('MainModelAssociation');
        $associationStubInReferencedModel = $this->buildAssociationStub('ReferencedModelAssociation');

        $edm->addAssociation($associationStubInMainModel);
        $referencedModel->addAssociation($associationStubInReferencedModel);

        $this->assertNull($edm->findAssociationByFullName('NonExistantName'));
        $this->assertSame($associationStubInMainModel, $edm->findAssociationByFullName(
            'Self.MainModelAssociation'));
        $this->assertSame($associationStubInMainModel, $edm->findAssociationByFullName(
            'MainModelAssociation'));
        $this->assertNull($edm->findAssociationByFullName('ReferencedModelAssociation'));
        $this->assertSame($associationStubInReferencedModel, $edm->findAssociationByFullName(
            'Referenced.ReferencedModelAssociation'));
    }

    public function testFindEntityContainerByFullName()
    {
        $edm = new EntityDataModel('http://some.url.com', 'Fully.Qualified.Namespace', 'Self');

        $referencedModel = new EntityDataModel('http://referenced.url.com', 'Referenced.Namespace');

        $edm->addReferencedModel($referencedModel, 'Referenced');

        $entityContainerStubInMainModel = $this->buildEntityContainerStub('MainModelEntityContainer');
        $entityContainerStubInReferencedModel = $this->buildEntityContainerStub('ReferencedModelEntityContainer');

        $edm->addEntityContainer($entityContainerStubInMainModel);
        $referencedModel->addEntityContainer($entityContainerStubInReferencedModel);

        $this->assertNull($edm->findEntityContainerByFullName('NonExistantName'));
        $this->assertSame($entityContainerStubInMainModel, $edm->findEntityContainerByFullName(
            'Self.MainModelEntityContainer'));
        $this->assertSame($entityContainerStubInMainModel, $edm->findEntityContainerByFullName(
            'MainModelEntityContainer'));
        $this->assertNull($edm->findEntityContainerByFullName('ReferencedModelEntityContainer'));
        $this->assertSame($entityContainerStubInReferencedModel, $edm->findEntityContainerByFullName(
            'Referenced.ReferencedModelEntityContainer'));
    }

    public function invalidNamespaceProvider()
    {
        return array(
            array('A-dashed-name'),
            array('NameWithS%mbol'),
            array('Name With Spaces')
        );
    }

    protected function buildStructuralTypeStub($name, $className)
    {
        $structuralTypeStub = $this->getMockBuilder('Rolab\EntityDataModel\Type\StructuralType')
            ->disableOriginalConstructor()
            ->setMethods(array('setEntityDataModel', 'getName', 'getClassName'))
            ->getMockForAbstractClass();

        $structuralTypeStub->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name));

        $structuralTypeStub->expects($this->any())
            ->method('getClassName')
            ->will($this->returnValue($className));

        return $structuralTypeStub;
    }

    protected function buildAssociationStub($name)
    {
        $associationStub = $this->getMockBuilder('Rolab\EntityDataModel\Association')
            ->disableOriginalConstructor()
            ->setMethods(array('setEntityDataModel', 'getName'))
            ->getMock();

        $associationStub->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name));

        return $associationStub;
    }

    protected function buildEntityContainerStub($name)
    {
        $entityContainerStub = $this->getMockBuilder('Rolab\EntityDataModel\EntityContainer')
            ->disableOriginalConstructor()
            ->setMethods(array('setEntityDataModel', 'getName'))
            ->getMock();

        $entityContainerStub->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name));

        return $entityContainerStub;
    }
}
