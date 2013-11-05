<?php

/*
 * This file is part of the Rolab Entity Data Model library.
 *
 * (c) Roland Schermer <roland0507@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rolab\EntityDataModel;

use ODataProducer\Provider\Metadata\StructuralType;

use Rolab\EntityDataModel\EntityContainer;
use Rolab\EntityDataModel\Exception\InvalidArgumentException;

class EntityDataModel
{
    private $entityContainers;
	
	private $structuralTypes;
	
	private $structuralTypesByClassName;
	
	private $defaultEntityContainer;
	
	public function setEntityContainers($entityContainers)
	{
		$entityContainers = is_array($entityContainers) ? $entityContainers : array($entityContainers);
		
		foreach ($entityContainers as $entityContainer) {
			$this->addEntityContainer($entityContainer);
		}
	}
	
	public function addEntityContainer(EntityContainer $entityContainer)
	{
		if (isset($this->entityContainers[$entityContainer->getName()])) {
			throw new InvalidArgumentException(sprintf('The entity data model already has a container by the name "%s"', 
				$entityContainer->getName()));
		}
		
		$this->entityContainers[$entityContainer->getName()] = $entityContainer;
	}
	
	public function removeEntityContainer($containerName)
	{
		unset($this->entityContainers[$containerName]);
	}
	
	public function getEntityContainers()
	{
		return $this->entityContainers;
	}
	
	public function getEntityContainerByName($name)
	{
		return $this->entityContainers[$name];
	}
	
	public function setDefaultContainer($containerName)
	{
		if (empty($this->entityContainers[$containerName])) {
			throw new InvalidArgumentException(sprintf('Entity data model does not have container by the name "%s".', 
				$containerName));
		}
		
		$this->defaultEntityContainer = $this->entityContainers[$containerName];
	}
	
	public function getDefaultEntityContainer()
	{
		$containers = array_values($this->entityContainers);
		return isset($this->defaultEntityContainer) ? $this->defaultEntityContainer : $containers[0];
	}
	
	public function getEntitySetByName($name)
	{
		if (strpos($name, '.')) {
			list($containerName, $setName) = explode('.', $name);
			$container = $this->getEntityContainerByName($containerName);
		} else {
			$setName = $name;
			$container = $this->getDefaultEntityContainer();
		}
		
		if (isset($container)) {
			return $container->getEntitySetByName($setName);
		}
		
		return null;
	}
	
	public function setStructuralTypes($structuralTypes)
	{
		$structuralTypes = is_array($structuralTypes) ? $structuralTypes : array($structuralTypes);
		
		unset($this->structuralTypes);
		unset($this->structuralTypesByClassName);
		
		foreach ($structuralTypes as $structuralType) {
			$this->addStructuralType($structuralType);
		}
	}
	
	public function addStructuralType(StructuralType $structuralType)
	{
		if (isset($this->structuralTypes[$structuralType->getFullName()])) {
			throw new InvalidArgumentException(sprintf('The entity data model already has a type by the name "%s"', 
				$structuralType->getFullName()));
		}
		
		$this->structuralTypes[$structuralType->getFullName()] = $structuralType;
		$this->structuralTypesByClassName[$structuralType->getClassName()] = $structuralType;
	}
	
	public function removeStructuralType($structuralTypeName)
	{
		if ($structuralType = $this->getStructuralTypeByName($structuralTypeName)) {
			unset($this->structuralTypes[$structuralType->getFullName()]);
			unset($this->structuralTypesByClassName[$structuralType->getClassName()]);
		}
	}
	
	public function getStructuralTypes()
	{
		return $this->structuralTypes;
	}
	
	public function getStructuralTypeByName($name)
	{
		return $this->structuralTypes[$name];
	}
	
	public function getStructuralTypeByClassName($className)
	{
		return $this->structuralTypesByClassName[$className];
	}
}
