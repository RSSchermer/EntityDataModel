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

use Rolab\EntityDataModel\Type\StructuralPropertyDescription;
use Rolab\EntityDataModel\Type\ComplexType;

class ComplexPropertyDescription extends StructuralPropertyDescription
{
    public function __construct($name, \ReflectionProperty $reflection, ComplexType $type, $isCollection = false)
    {
        parent::__construct($name, $reflection, $type, $isCollection);
    }
}
