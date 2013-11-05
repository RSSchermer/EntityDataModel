<?php

namespace Rolab\EntityDataModel\Builder;

abstract class PropertyDefinition
{
	private $name;
	
	private $typeName;
	
	private $isCollection;
	
	public function __construct($name, $typeName, $isCollection = false)
	{
		$this->name = $name;
		$this->typeName = $typeName;
		$this->isCollection = $isCollection;
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function getTypeName()
	{
		return $this->typeName;
	}
	
	public function isCollection()
	{
		return (bool) $this->isCollection;
	}
}
