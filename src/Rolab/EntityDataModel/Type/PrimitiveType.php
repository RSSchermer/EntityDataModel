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

use Rolab\EntityDataModel\NamedModelConstruct;
use Rolab\EntityDataModel\Type\ResourceType;

/**
 * Represents a primitive data type. A primitive type is atomic and
 * cannot be decomposed in to smaller types.
 * 
 * @author Roland Schermer <roland0507@gmail.com>
 */
abstract class PrimitiveType extends NamedModelConstruct implements ResourceType
{
}
