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

use Rolab\EntityDataModel\EntitySet;
use Rolab\EntityDataModel\Exception\InvalidArgumentException;

class EntityContainer
{
    private $name;

    private $parentContainer;

    private $entityDataModel;

    private $entitySets = array();

    private $associationSets = array();

    public function __construct($name, EntityContainer $parentContainer = null)
    {
        if (!preg_match('/^[A-Za-z0-9_]+$/', $name)) {
            throw new InvalidArgumentException(sprintf('"%s" is an illegal name for a container. The name for ' .
                'a container may only contain alphanumeric characters and underscores.', $name));
        }

        $this->name = $name;
        $this->parentContainer = $parentContainer;
    }

    public function setEntityDataModel(EntityDataModel $entityDataModel)
    {
        $this->entityDataModel = $entityDataModel;
    }

    public function getEntityDataModel()
    {
        return $this->entityDataModel;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getFullName()
    {
        return isset($this->entityDataModel) ? $this->entityDataModel->getNamespace() .'.'. $this->name : $this->name;
    }

    public function getParentContainer()
    {
        return $this->parentContainer;
    }

    public function addEntitySet(EntitySet $entitySet)
    {
        if (isset($this->entitySets[$entitySet->getName()])) {
            throw new InvalidArgumentException(sprintf(
                'The entity container already contains an entity set by the name "%s"',
                $entitySet->getName()
            ));
        }

        $this->entitySets[$entitySet->getName()] = $entitySet;

        $entitySet->setEntityContainer($this);
    }

    public function getEntitySets()
    {
        return isset($this->parentContainer) ?
            array_merge($this->parentContainer->getEntitySets(), $this->entitySets) : $this->entitySets;
    }

    public function getEntitySetByName($name)
    {
        $entitySets = isset($this->parentContainer) ?
            array_merge($this->parentContainer->getEntitySets(), $this->entitySets) : $this->entitySets;

        return isset($entitySets[$name]) ? $entitySets[$name] : null;
    }

    public function addAssociationSet(AssociationSet $associationSet)
    {
        if (isset($this->associationSets[$associationSet->getName()])) {
            throw new InvalidArgumentException(sprintf(
                'The entity container already contains an association set by the name "%s"',
                $associationSet->getName()
            ));
        }

        $this->associationSets[$associationSet->getName()] = $associationSet;

        $associationSet->setEntityContainer($this);
    }

    public function getAssociationSets()
    {
        return isset($this->parentContainer) ?
            array_merge($this->parentContainer->getAssociationSets(), $this->associationSets) : $this->associationSets;
    }

    public function getAssociationSetByName($associationSetName)
    {
        $associationSets = isset($this->parentContainer) ?
            array_merge($this->parentContainer->getAssociationSets(), $this->associationSets) : $this->associationSets;

        return isset($associationSets[$associationSetName]) ? $associationSets[$associationSetName] : null;
    }
}
