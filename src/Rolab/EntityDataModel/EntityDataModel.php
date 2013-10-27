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

use ODataProducer\Provider\Metadata\complexType;

use Rolab\EntityDataModel\EntityContainer;

class EntityDataModel
{
    private $entityContainers;
	
	private $complexTypes;
	
	private $complexTypesByClassName;
	
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
			throw new \InvalidArgumentException(sprintf('The entity data model already has a container by the name "%s"', 
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
			throw new \InvalidArgumentException(sprintf('Entity data model does not have container by the name "%s".', 
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
	
	public function setComplexTypes($complexTypes)
	{
		$complexTypes = is_array($complexTypes) ? $complexTypes : array($complexTypes);
		
		unset($this->complexTypes);
		unset($this->complexTypesByClassName);
		
		foreach ($complexTypes as $complexType) {
			$this->addComplexType($complexType);
		}
	}
	
	public function addComplexType(ComplexType $complexType)
	{
		if (isset($this->complexTypes[$complexType->getFullName()])) {
			throw new \InvalidArgumentException(sprintf('The entity data model already has a type by the name "%s"', 
				$complexType->getFullName()));
		}
		
		$this->complexTypes[$complexType->getFullName()] = $complexType;
		$this->complexTypesByClassName[$complexType->getClassName()] = $complexType;
	}
	
	public function removeComplexType($complexTypeName)
	{
		if ($complexType = $this->getcomplexTypeByName($complexTypeName)) {
			unset($this->complexTypes[$complexType->getFullName()]);
			unset($this->complexTypesByClassName[$complexType->getClassName()]);
		}
	}
	
	public function getComplexTypes()
	{
		return $this->complexTypes;
	}
	
	public function getComplexTypeByName($name)
	{
		return $this->complexTypes[$name];
	}
	
	public function getComplexTypeByClassName($className)
	{
		return $this->complexTypesByClassName[$className];
	}
}
