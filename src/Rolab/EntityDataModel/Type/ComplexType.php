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

use Rolab\EntityDataModel\Type\StructuralType;

class ComplexType extends StructuralType
{
    public function __construct($name, $namespace, \ReflectionClass $reflection, array $propertyDescriptions = array(),
        ComplexType $baseType = null
    ){
        parent::__construct($name, $namespace, $reflection, $propertyDescriptions, $baseType);
    }
}
