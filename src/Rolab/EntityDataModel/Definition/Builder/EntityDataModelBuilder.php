<?php

namespace Rolab\EntityDataModel\Definition\Builder;

use Metadata\MetadataFactory;

use Rolab\EntityDataModel\Definition\StructuralTypeDefinitionFactory;

class EntityDataModelBuilder
{
    /**
     * @var StructuralTypeDefinitionFactory
     */
    private $metadataFactory;

    private $autoLoadMissingReferences;

    private $baseEntityDataModel;

    private $structuralTypeMetadata = array();

    private $entityContainerDefinitions = array();

    public function __construct(MetadataFactory $metadataFactory, $autoLoadMissingReferences = true)
    {
        $this->metadataFactory = $metadataFactory;
        $this->autoLoadMissingReferences = $autoLoadMissingReferences;
    }

    public function setBaseEntityDataModel(EntityDataModel $baseEntityDataModel)
    {
        $this->baseEntityDataModel = $baseEntityDataModel;
    }

    public function addStructuralTypeMetadata(StructuralTypeMetadata $structuralTypeMetadata)
    {
        $this->structuralTypeDefinitions[$structuralTypeMetadata->name] = $structuralTypeDefinition;
    }

    public function addStructuralTypeMetadataForClass($className)
    {
        if ($metadata = $this->metadataFactory->getMetadataForClass($className)) {
            $this->structuralTypeDefinitions[$metadata->name] = $metadata;
        }
    }

    public function addEntityContainerDefinition(EntityContainerDefinition $entityContainerDefinition)
    {
        
    }

    public function buildEntityDataModel()
    {

    }
}
