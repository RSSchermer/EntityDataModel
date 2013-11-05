<?php

/*
 * This file is part of the Rolab Entity Data Model library.
 *
 * (c) Roland Schermer <roland0507@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rolab\EntityDataModel\Type;

use Rolab\EntityDataModel\Type\Type\StructuralType;

class ComplexType extends StructuralType
{
	public function __construct($className, $name, $namespace, array $properties = array(), ComplexType $baseType = null)
	{
		parent::__construct($className, $name, $namespace, $properties, $baseType);
	}
}
