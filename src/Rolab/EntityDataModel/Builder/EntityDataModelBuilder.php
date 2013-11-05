<?php

/*
 * This file is part of the Rolab Entity Data Model library.
 *
 * (c) Roland Schermer <roland0507@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rolab\EntityDataModel\Builder;

class EntityDataModelBuidler
{
	private $baseEntityDataModel;
	
	private $entityDataModelDefinition;
	
	private $structuralTypeBuilder;
	
	private $entityContainerBuilder;
	
	public function __construct()
	{
		$this->entityDataModelDefinition = new EntityDataModelDefinition();
	}
	
	public function setEntityDataModelDefinition(EntityDataModelDefinition $entityDataModelDefinition)
	{
		$this->entityDataModelDefinition = $entityDataModelDefinition;
	}
	
	public function setBaseEntityDataModel(EntityDataModel $entityDataModel)
	{
		$this->baseEntityDataModel = $entityDataModel;
	}
	
	public function setStructuralTypeBuilder(StructuralTypeBuilder $structuralTypeBuidler)
	{
		$this->structuralTypeBuilder = $structuralTypeBuidler;
	}
	
	public function setEntityContainerBuilder(EntityContainerBuilder $entityContainerBuilder)
	{
		$this->entityContainerBuilder = $entityContainerBuilder;
	}
	
	public function structuralTypes()
	{
		if (null === $this->structuralTypeBuilder) {
			$this->structuralTypeBuilder = new StructuralTypeBuilder($this);
		}
		
		return $this->structuralTypeBuilder;
	}
	
	public function addStructuralType(StructuralTypeDefinition $structuralType)
	{
		$this->entityDataModelDefinition->addStructuralTypeDefinition($structuralType);
	}
	
	public function entityContainers()
	{
		if (null === $this->entityContainerBuilder) {
			$this->entityContainerBuilder = new EntityContainerBuilder($this);
		}
		
		return $this->entityContainerBuilder;
	}
	
	public function addEntityContainer(EntityContainerDefinition $entityContainer)
	{
		$this->entityDataModelDefinition->addEntityContainerDefinition($entityContainer);
	}
	
	public function buildEntityDataModel()
	{
		
	}
}
