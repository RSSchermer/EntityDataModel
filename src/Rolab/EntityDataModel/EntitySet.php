<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel;

use PhpCollection\Map;
use PhpCollection\MapInterface;
use PhpOption\Option;

use Rolab\EntityDataModel\Exception\RuntimeException;
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
     * @var MapInterface
     */
    private $navigationPropertyBindings;

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
        $this->navigationPropertyBindings = new Map();
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
     * @param string    $propertyDescriptionName The name of navigation property on this entity set's entity
     *                                           type that is to be bound.
     * @param EntitySet $targetSet               The target entity set to bind the navigation property to.
     *
     * @throws InvalidArgumentException Thrown if the navigation property is not defined on this entity set's entity
     *                                  type.
     *                                  Thrown if the target entity set is not contained in the same container as this
     *                                  entity type.
     *                                  Thrown if the target entity set's entity type is not a subtype of the navigation
     *                                  property's value type.
     */
    public function bindNavigationProperty(string $propertyDescriptionName, EntitySet $targetSet)
    {
        $navigationProperty = $this->entityType->getPropertyDescriptionByName($propertyDescriptionName)
            ->getOrThrow(new InvalidArgumentException(sprintf(
                'Tried to bind a property named "%s" on entity set "%s", but the entity type for this entity set ' .
                '("%s") does not define a property with that name.',
                $propertyDescriptionName,
                $this->getName(),
                $this->entityType->getName()
            )));

        if (!$navigationProperty instanceof NavigationPropertyDescription) {
            throw new InvalidArgumentException(sprintf(
                'Tried to bind a property named "%s" on entity set "%s" with entity type "%s%, but this property ' .
                'is not a navigation property.',
                $propertyDescriptionName,
                $this->getName(),
                $this->entityType->getName()
            ));
        }

        $navigationPropertyOwnerType = $navigationProperty->getStructuredType()
            ->getOrThrow(new InvalidArgumentException(
                'Cannot bind a navigation property for which an owner entity type has not yet been specified. ' .
                'Call `setStructuredType` to set an entity type that owns the navigation property before trying to ' .
                'bind it on an entity set.'
            ));

        if (!$this->getEntityType()->isSubTypeOf($navigationPropertyOwnerType)) {
            throw new InvalidArgumentException(sprintf(
                'Tried to set a binding for navigation property "%s" on entity set "%s" for entity type "%s", but ' .
                'this navigation property is not defined on that entity type. You can only bind a navigation ' .
                'on an entity set if that property is defined on the entity set\'s entity type.',
                $navigationProperty->getName(),
                $this->getName(),
                $this->getEntityType()->getFullName()
            ));
        }

        if (!$targetSet->getEntityType()->isSubTypeOf($navigationProperty->getPropertyValueType())) {
            throw new InvalidArgumentException(sprintf(
                'Tried to bind entity set "%s", defined on entity type "%s", to navigation property "%s" with ' .
                'property value type "%s". Cannot bind a navigation property to an entity set on an entity type ' .
                'that is not a subtype of the navigation property\'s value type.',
                $targetSet->getName(),
                $targetSet->getEntityType()->getFullName(),
                $navigationProperty->getName(),
                $navigationProperty->getPropertyValueType()->getFullName()
            ));
        }

        $entityContainer = $this->getEntityContainer()->getOrThrow(new RuntimeException(
            'Cannot bind a navigation property on an entity set for which no entity container has been specified.' .
            'Call `setEntityContainer` to set an entity container on the entity set before trying to bind a ' .
            'navigation property on it.'
        ));

        $targetSetContainer = $targetSet->getEntityContainer()->getOrThrow(new InvalidArgumentException(
            'Cannot bind a navigation property to an entity set for which no entity container has been specified.' .
            'Call `setEntityContainer` to set an entity container on the target entity set before trying to bind ' .
            'it to a navigation property.'
        ));

        if (!$targetSet->isContainedIn($entityContainer)) {
            throw new InvalidArgumentException(sprintf(
                'Tried to bind navigation property "%s" to target entity set "%s" for origin entity set "%s", but ' .
                'the target entity set is not contained in the same container ("%s") as the origin entity set ' .
                '("%s"). A navigation property can only be bound to a target entity set in the same entity container.',
                $navigationProperty->getName(),
                $targetSet->getName(),
                $this->getName(),
                $targetSetContainer->getFullName(),
                $entityContainer->getFullName()
            ));
        }

        $this->navigationPropertyBindings->set($propertyDescriptionName, $targetSet);
    }

    /**
     * Returns a map describing the navigation property bindings where the key is
     * the navigation property description's name and the value is the target
     * entity set
     *
     * @return MapInterface A map describing the navigation property bindings where the key is
     *                      the navigation property description's name and the value is the target
     *                      entity set
     */
    public function getNavigationPropertyBindings() : MapInterface
    {
        return $this->navigationPropertyBindings;
    }

    /**
     * Returns the target set for the navigation property wrapped in Some, or None if no
     * binding exists for the navigation property.
     *
     * @param NavigationPropertyDescription $navigationProperty The navigation property to resolve the bound target
     *                                                          entity set for.
     *
     * @return Option The target set for the navigation property wrapped in Some, or None if no
     *                binding exists for the navigation property.
     */
    public function getNavigationPropertyBindingFor(NavigationPropertyDescription $navigationProperty) : Option
    {
        return $this->navigationPropertyBindings->get($navigationProperty->getName());
    }
}
