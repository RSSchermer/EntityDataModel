<?php

/*
 * This file is part of the Rolab Entity Data Model library.
 *
 * (c) Roland Schermer <roland0507@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rolab\EntityDataModel\Builder;

use Rolab\EntityDataModel\Builder\PropertyDefinition;

abstract class RegularPropertyDefinition extends PropertyDefinition
{
    private $typeName;

    public function __construct($name, $typeName, $isCollection = false)
    {
        parent::__construct($name, $isCollection);

        $this->typeName = $typeName;
    }

    public function getTypeName()
    {
        return $this->typeName;
    }
}
