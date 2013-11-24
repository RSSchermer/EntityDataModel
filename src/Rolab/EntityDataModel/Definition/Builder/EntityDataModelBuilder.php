<?php

namespace Rolab\EntityDataModel\Definition\Builder;

use Rolab\EntityDataModel\Definition\StructuralTypeDefinitionFactory;

class EntityDataModelBuilder
{
    private $structuralTypeDefinitionFactory;

    private $autoLoadMissingReferences;

    private $baseEntityDataModel;

    private $structuralTypeDefinitions = array();

    private $entityContainerDefinitions = array();

    public function __construct(StructuralTypeDefinitionFactory $structuralTypeDefinitionFactory,
        $autoLoadMissingReferences = true
    ) {
        $this->structuralTypeDefinitionFactory = $structuralTypeDefinitionFactory;
        $this->autoLoadMissingReferences = $autoLoadMissingReferences;
    }

    public function setBaseEntityDataModel(EntityDataModel $baseEntityDataModel)
    {
        $this->baseEntityDataModel = $baseEntityDataModel;
    }

    public function addStructuralTypeDefinition(StructuralTypeDefinition $structuralTypeDefinition)
    {

    }

    public function addStructuralTypeDefinitionForClass($className)
    {

    }

    public function addEntityContainerDefinition(EntityContainerDefinition $entityContainerDefinition)
    {

    }

    public function buildEntityDataModel()
    {

    }
}
