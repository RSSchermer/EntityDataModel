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

use Rolab\EntityDataModel\Property\RegularProperty;
use Rolab\EntityDataModel\Type\PrimitiveType;

class PrimitiveProperty extends RegularProperty
{
    public function __construct($name, PrimitiveType $type, $isCollection = false)
    {
        parent::__construct($name, $type, $isCollection);
    }
}
