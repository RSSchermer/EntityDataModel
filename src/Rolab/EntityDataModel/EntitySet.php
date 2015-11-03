<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel;

use Rolab\EntityDataModel\Type\EntityType;
use Rolab\EntityDataModel\Exception\InvalidArgumentException;
use Rolab\EntityDataModel\Type\NavigationPropertyDescription;

/**
 * Represents a set of entity instances of a certain entity type.
 *
 * @author Roland Schermer <roland0507@gmail.com>
 */
class EntitySet extends NamedContainerElement
{
    /**
     * @var EntityType
     */
    private $entityType;

    /**
     * @var NavigationPropertyBinding[]
     */
    private $navigationPropertyBindings = array();

    /**
     * Creates a new entity set.
     * 
     * @param string     $name       The name of the entity set (must contain only alphanumber
     *                               characters and underscores).
     * @param EntityType $entityType The entity type that the entities this set represents are
     *                               instances of.
     * 
     * @throws InvalidArgumentException Thrown if the container's name contains illegal characters.
     */
    public function __construct(string $name, EntityType $entityType)
    {
        parent::__construct($name);
        
        $this->entityType = $entityType;
    }
    
    /**
     * Returns the entity type that all entities represented by the entity set are instances of.
     * 
     * @return EntityType The entity type that all entities represented by the entity set are
     *                    instances of.
     */
    public function getEntityType() : EntityType
    {
        return $this->entityType;
    }

    public function bindNavigationProperty(NavigationPropertyDescription $navigationProperty, EntitySet $targetSet)
    {
        if ($navigationProperty->getStructuredType() !== $this->entityType) {
            throw new InvalidArgumentException(
                'Can only bind a navigation property that is defined on the entity type represented by this entity ' .
                'set.'
            );
        }

        if ($navigationProperty->getPropertyValueType() !== $targetSet->getEntityType()) {
            throw new InvalidArgumentException(
                'Can only bind a navigation property to a target entity set if the entity type of that set is equal ' .
                'to the navigation property descriptions property type.'
            );
        }

        $this->navigationPropertyBindings[$navigationProperty->getName()] =
            new NavigationPropertyBinding($navigationProperty, $targetSet);
    }

    public function getNavigationPropertyBindings() : array
    {
        return $this->navigationPropertyBindings;
    }
}
