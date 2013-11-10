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

use Rolab\EntityDataModel\Builder\StructuralTypeDefinition;
use Rolab\EntityDataModel\Builder\EntityContainerDefinition;

class EntityDataModelDefinition
{
    private $structuralTypeDefinitions = array();

    private $entityContainerDefinitions = array();

    private $defaultContainerName;

    public function addStructuralTypeDefinition(StructuralTypeDefinition $structuralTypeDefinition)
    {
        $this->structuralTypeDefinition[] = $structuralTypeDefinition;
    }

    public function getStructuralTypeDefinitions()
    {
        return $this->structuralTypeDefinitions;
    }

    public function addEntityContainerDefinition(EntityContainerDefinition $entityContainerDefinition)
    {
        $this->entityContainerDefinitions[] = $entityContainerDefinition;
    }

    public function getEntityContainerDefinitions()
    {
        return $this->entityContainerDefinitions;
    }

    public function setDefaultContainer($containerName)
    {
        $this->defaultContainerName = $containerName;
    }

    public function getDefaultContainer()
    {
        return $this->defaultContainerName;
    }
}
