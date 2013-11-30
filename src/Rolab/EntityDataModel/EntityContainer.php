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

/**
 * Defines a logical grouping of entities and their associations. Is used
 * in the OData protocol to determine accessibility and uri's for entity
 * resources. An entity container may have a parent container. Entity sets
 * and association sets in the child container may then references any of 
 * the entity sets and association sets in the parent container. 
 *
 * @author Roland Schermer <roland0507@gmail.com>
 */
class EntityContainer
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var EntityContainer
     */
    private $parentContainer;

    /**
     * @var EntityDataModel
     */
    private $entityDataModel;

    /**
     * @var array
     */
    private $entitySets = array();
    
    /**
     * @var array
     */
    private $associationSets = array();
    
    /**
     * Creates a new entity container.
     * 
     * Creates a new entity container. A parent container may be specified, in which
     * case entity sets and association sets in the container may reference any of the
     * entity sets and association sets in the parent container.
     * 
     * @param string               $name            The name of the entity container (must container 
     *                                              only alphanumber characters and underscores).
     * @param null|EntityContainer $parentContainer An optional parent container for the entity 
     *                                              container
     */
    public function __construct($name, EntityContainer $parentContainer = null)
    {
        if (!preg_match('/^[A-Za-z0-9_]+$/', $name)) {
            throw new InvalidArgumentException(sprintf('"%s" is an illegal name for a container. The name for ' .
                'a container may only contain alphanumeric characters and underscores.', $name));
        }

        $this->name = $name;
        $this->parentContainer = $parentContainer;
    }
    
    /**
     * Sets the entity data model the container is a part of.
     * 
     * Sets the entity data model the container is a part of. An entity container should
     * always be part of some entity data model.
     * 
     * @param EntityDataModel $entityDataModel The entity data model the entity container is
     *                                         a part of.
     */
    public function setEntityDataModel(EntityDataModel $entityDataModel)
    {
        $this->entityDataModel = $entityDataModel;
    }
    
    /**
     * Returns the entity data model the entity container is a part of.
     * 
     * @return null|EntityDataModel The entity data model the entity container is a part of
     *                              or null if no entity data model is assigned yet.
     */
    public function getEntityDataModel()
    {
        return $this->entityDataModel;
    }
    
    /**
     * 
     */
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
