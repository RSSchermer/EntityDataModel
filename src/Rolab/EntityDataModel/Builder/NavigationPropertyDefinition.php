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

class NavigationPropertyDefinition extends PropertyDefinition
{
    private $entitySetName;

    public function __construct($name, $entitySetName, $isCollection = false)
    {
        parent::__construct($name, $isCollection);

        $this->entitySetName = $entitySetName;
    }

    public function getEntitySetName()
    {
        return $this->entitySetName;
    }
}
