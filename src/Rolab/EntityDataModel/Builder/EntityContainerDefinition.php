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

use Rolab\EntityDataModel\Builder\EntityDataModelBuilder;
use Rolab\EntityDataModel\Builder\EntitySetDefinition;

class EntityContainerDefinition
{
    private $name;

    private $namespace;

    private $parentContainerName;

    private $entitySetDefinitions;

    private $entityDataModelBuilder;

    public function __construct($name, $namespace, $parentContainerName = null)
    {
        $this->name = $name;
        $this->namespace = $namespace;
        $this->parentContainerName = $parentContainerName;
    }

    public function setEntityDataModelBuilder(EntityDataModelBuilder $entityDataModelBuilder)
    {
        $this->entityDataModelBuilder = $entityDataModelBuilder;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function getParentContainerName()
    {
        return $this->parentContainerName;
    }

    public function addEntitySetDefinition(EntitySetDefinition $entitySetDefinition)
    {
        $this->entitySetDefinitions[] = $entitySetDefinition;
    }

    public function EntitySet($name, $entityTypeName)
    {
        $this->addEntitySetDefinition(new EntitySetDefinition($name, $entityTypeName));
    }

    public function getEntitySetDefinitions()
    {
        return $this->entitySetDefinitions;
    }

    public function end()
    {
        $this->entityDataModelBuilder->addEntityContainer($this);

        return $this->entityDataModelBuilder;
    }
}
