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

class EntitySetDefinition
{
    private $name;

    private $entityTypeName;

    public function __construct($name, $enityTypeName)
    {
        $this->name = $name;
        $this->entityTypeName = $enityTypeName;
    }

    public function getName()
    {
        return $name;
    }

    public function getEntityTypeName()
    {
        return $entityTypeName;
    }
}
