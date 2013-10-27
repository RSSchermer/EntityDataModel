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

use Rolab\EntityDataModel\EntityContainer;
use Rolab\EntityDataModel\Type\EntityType;

class EntitySet
{
	private $name;
	
	private $entityType;
	
	private $entityContainer;
	
	public function __construct($name, EntityType $entityType, EntityContainer $entityContainer)
	{
		$this->name = $name;
		$this->entityType = $entityType;
		$this->entityContainer = $entityContainer;
	}
	
	public function getEntityContainer()
	{
		return $this->entityContainer;
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function getEntityType()
	{
		return $this->entityType;
	}
}
