<?php

/*
 * This file is part of the Rolab Entity Data Model library.
 *
 * (c) Roland Schermer <roland0507@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rolab\EntityDataModel;

use Rolab\EntityDataModel\EntityContainer;
use Rolab\EntityDataModel\Type\EntityType;
use Rolab\EntityDataModel\Exception\InvalidArgumentException;

class EntitySet
{
    private $name;

    private $entityType;

    private $entityContainer;

    public function __construct($name, EntityType $entityType)
    {
        if (!preg_match('/^[A-Za-z0-9_]+$/', $name)) {
            throw new InvalidArgumentException(sprintf('"%s" is an illegal name for an entity set. The name for ' .
                'an entity set may only contain alphanumeric characters and underscores.', $name));
        }

        $this->name = $name;
        $this->entityType = $entityType;
    }

    public function setEntityContainer(EntityContainer $entityContainer)
    {
        $this->entityContainer = $entityContainer;
    }

    public function getEntityContainer()
    {
        return $this->entityContainer;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getEntityType()
    {
        return $this->entityType;
    }

}
