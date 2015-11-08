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

    /**
     * Binds a navigation property description on this entity sets entity type to a target entity set in this
     * container.
     *
     * @param NavigationPropertyDescription $navigationProperty The navigation property on this entity sets entity
     *                                                          type that is to be bound.
     * @param EntitySet $targetSet                              The target entity set to bind the navigation property
     *                                                          to.
     *
     * @throws InvalidArgumentException Thrown if the navigation property is not defined on this entity set's entity
     *                                  type.
     *                                  Thrown if the target entity set is not contained in the same container as this
     *                                  entity type.
     *                                  Thrown if the target entity set's entity type is not a subtype of the navigation
     *                                  property's value type.
     */
    public function bindNavigationProperty(NavigationPropertyDescription $navigationProperty, EntitySet $targetSet)
    {
        if (!$this->getEntityType()->isSubTypeOf($navigationProperty->getStructuredType())) {
            throw new InvalidArgumentException(sprintf(
                'Tried to set a binding for navigation property "%s" on entity set "%s" for entity type "%s", but ' .
                'this navigation property is not defined on that entity type. You can only bind a navigation ' .
                'on an entity set if that property is defined on the entity set\'s entity type.',
                $navigationProperty->getName(),
                $this->getName(),
                $this->getEntityType()->getFullName()
            ));
        }

        if (!$targetSet->isContainedIn($this->getEntityContainer())) {
            throw new InvalidArgumentException(sprintf(
                'Tried to bind navigation property "%s" to target entity set "%s" for origin entity set "%s", but ' .
                'the target entity set is not contained in the same container ("%s") as the origin entity set ' .
                '("%s"). A navigation property can only be bound to a target entity set in the same entity container.',
                $navigationProperty->getName(),
                $targetSet->getName(),
                $this->getName(),
                $targetSet->getEntityContainer()->getFullName(),
                $this->getEntityContainer()->getFullName()
            ));
        }

        $this->navigationPropertyBindings[$navigationProperty->getName()] =
            new NavigationPropertyBinding($navigationProperty, $targetSet);
    }

    /**
     * Returns the navigation property bindings for this entity set.
     *
     * @return NavigationPropertyBinding[] The navigation property bindings for this entity set.
     */
    public function getNavigationPropertyBindings() : array
    {
        return $this->navigationPropertyBindings;
    }
}
