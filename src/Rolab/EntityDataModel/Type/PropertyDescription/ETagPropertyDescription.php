<?php

/*
 * This file is part of the Rolab Entity Data Model library.
 *
 * (c) Roland Schermer <roland0507@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rolab\EntityDataModel\Type\PropertyDescription;

use Rolab\EntityDataModel\Type\PrimitiveType;
use Rolab\EntityDataModel\Type\PropertyDescription\PrimitivePropertyDescription;

class ETagPropertyDescription extends PrimitivePropertyDescription
{
    public function __construct($name, \ReflectionProperty $reflection, PrimitiveType $type)
    {
        parent::__construct($name, $reflection, $type, false);
    }
}
