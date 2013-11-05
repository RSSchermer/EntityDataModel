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

use Rolab\EntityDataModel\Property\ResourceProperty;
use Rolab\EntityDataModel\Type\EntityType;
use Rolab\EntityDataModel\EntitySet;

class NavigationProperty extends ResourceProperty
{
	private $targetEntitySet;
	
	public function __construct($name, EntitySet $targetEntitySet, $isCollection = false)
	{
		parent::__construct($name, $targetEntitySet->getType(), $isCollection);
		
		$this->targetEntitySet = $targetEntitySet;
	}
	
	public function getTargetEntitySet()
	{
		return $this->targetEntitySet;
	}
}
