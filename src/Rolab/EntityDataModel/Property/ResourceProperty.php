<?php

/*
 * This file is part of the Rolab Entity Data Model library.
 *
 * (c) Roland Schermer <roland0507@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rolab\EntityDataModel\Property;

use Rolab\EntityDataModel\Type\ResourceType;

abstract class ResourceProperty
{
	private $name;
	
	private $resourceType;
	
	private $isCollection;
	
	public function __construct($name, ResourceType $resourceType, $isCollection = false)
	{
		$this->name = $name;
		$this->resourceType = $resourceType;
		$this->isCollection = $isCollection;
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function getResourceType()
	{
		return $this->resourceType;
	}
	
	public function isCollection()
	{
		return (bool) $this->isCollection;
	}
}
