<?php

namespace Rolab\EntityDataModel\Builder;

use Rolab\EntityDataModel\Builder\PropertyDefinition;

class NavigationPropertyDefition
{
	private $name;
	
	private $setName;
	
	private $isCollection;
	
	public function __construct($name, $setName, $isCollection = false)
	{
		$this->name = $name;
		$this->setName = $setName;
		$this->isCollection = $isCollection;
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function getSetName()
	{
		return $this->setName;
	}
	
	public function isCollection()
	{
		return (bool) $this->isCollection;
	}
}
