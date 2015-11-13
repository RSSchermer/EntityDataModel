<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel\Type;

use PhpCollection\Map;
use PhpCollection\MapInterface;
use PhpOption\Option;

use Rolab\EntityDataModel\Exception\InvalidArgumentException;

/**
 * Represents an entity type.
 *
 * Entity types are structured types, for which one or more structural
 * properties define a key by which instances of this entity type can be
 * uniquely referenced. An entity type must either define one or more key
 * properties itself, or may inherit key properties from a base entity type.
 * An example would be a "Customer" type which, among other structural
 * properties, defines an "Id" property by which a customer instance is
 * uniquely identifiable.
 *
 * Apart from structural properties, an entity type can also define
 * navigation properties to specify relationships with other entity types.
 * 
 * @author Roland Schermer <roland0507@gmail.com>
 */
class EntityType extends ComplexType
{
    /**
     * @var MapInterface
     */
    private $navigationPropertyDescriptions;

    /**
     * @var MapInterface
     */
    private $keyPropertyDescriptions;

    /**
     * @var MapInterface
     */
    private $eTagPropertyDescriptions;

    /**
     * @var Option
     */
    private $baseType;

    /**
     * Creates a new entity type.
     *
     * @param string $name                                                    The name of the complex type (may only
     *                                                                        contain alphanumeric characters and the
     *                                                                        underscore).
     * @param \ReflectionClass $reflection                                    Reflection of the class this structural
     *                                                                        type maps to.
     * @param StructuralPropertyDescription[] $structuralPropertyDescriptions Descriptions for each of the structural
     *                                                                        properties.
     * @param NavigationPropertyDescription[] $navigationPropertyDescriptions Descriptions for each of the navigation
     *                                                                        properties.
     * @param EntityType $baseType                                            A base type this entity type extends and
     *                                                                        inherits all properties from.
     *
     * @throws InvalidArgumentException Thrown if the name contains illegal characters.
     *                                  Thrown if the property description list is empty.
     *
     */
    public function __construct(
        string $name,
        \ReflectionClass $reflection,
        array $structuralPropertyDescriptions,
        EntityType $baseType = null
    ) {
        $this->keyPropertyDescriptions = new Map();
        $this->eTagPropertyDescriptions = new Map();
        $this->navigationPropertyDescriptions = new Map();

        parent::__construct($name, $reflection, $structuralPropertyDescriptions);

        $this->baseType = Option::fromValue($baseType);

        if ($this->keyPropertyDescriptions->isEmpty() && $this->baseType->isEmpty()) {
            throw new InvalidArgumentException(sprintf(
                'Tried to define entity type "%s" without at least one key property and without a base entity type. ' .
                'An entity type must be defined with either at least one key property or a base entity type.',
                $this->getFullName()
            ));
        }
    }

    /**
     * Returns the base type this entity type extends and inherits all properties from.
     *
     * @return Option The base type this entity type extends wrapped in Some, or None if
     *                the entity type has no base type.
     */
    public function getBaseType() : Option
    {
        return $this->baseType;
    }

    /**
     * Returns whether or not this entity type is a subtype of the given entity type.
     *
     * This also returns true when given entity type is the same type as this entity type.
     *
     * @param EntityType $entityType The entity type for which to check if this entity type
     *                               is a subtype.
     *
     * @return bool Whether or not this entity type is a subtype of the given entity type.
     */
    public function isSubTypeOf(EntityType $entityType) : bool
    {
        if ($this === $entityType) {
            return true;
        }

        $parent = $this->getBaseType();

        while ($parent->isDefined()) {
            $p = $parent->get();

            if ($p === $entityType) {
                return true;
            }

            $parent = $p->getBaseType();
        }

        return false;
    }
    
    /**
     * Returns true if this entity type can be instantiated, false if it cannot.
     * 
     * @return boolean Whether or not this entity type can be instantiated.
     */
    public function isAbstract() : bool
    {
        return $this->getReflection()->isAbstract();
    }
    
    /**
     * {@inheritDoc}
     */
    public function addStructuralPropertyDescription(StructuralPropertyDescription $propertyDescription)
    {
        parent::addStructuralPropertyDescription($propertyDescription);

        if ($propertyDescription instanceof PrimitivePropertyDescription) {
            if ($propertyDescription->isPartOfKey()) {
                $this->keyPropertyDescriptions->set($propertyDescription->getName(), $propertyDescription);
            }

            if ($propertyDescription->isPartOfETag()) {
                $this->eTagPropertyDescriptions->set($propertyDescription->getName(), $propertyDescription);
            }
        }
    }

    /**
     * Adds a navigation property description to the complex type.
     *
     * Adds a navigation property description to the complex type. No two properties on the same
     * complex type may have the same name.
     *
     * @param NavigationPropertyDescription $propertyDescription The property description to
     *                                                           be added to the complex type.
     *
     * @throws InvalidArgumentException Thrown if the complex type already has a property with
     *                                  the same name.
     */
    public function addNavigationPropertyDescription(NavigationPropertyDescription $propertyDescription)
    {
        if ($this->getPropertyDescriptions()->containsKey($propertyDescription->getName())) {
            throw new InvalidArgumentException(sprintf(
                'Tried to add navigation property "%s" to entity type "%s", but this entity type already has a ' .
                'property with this name. The names of the properties defined on a structured type must be unique.',
                $propertyDescription->getName(),
                $this->getFullName()
            ));
        }

        $this->navigationPropertyDescriptions->set($propertyDescription->getName(), $propertyDescription);
        $propertyDescription->setStructuredType($this);
    }
    
    /**
     * {@inheritDoc}
     */
    public function getPropertyDescriptions() : MapInterface
    {
        $a = new Map();

        $a->addMap($this->getStructuralPropertyDescriptions());
        $a->addMap($this->getNavigationPropertyDescriptions());

        return $a;
    }

    /**
     * Returns true if this entity type has one or more e-tag properties, false if it does not.
     *
     * @return bool Whether or not this entity type has any e-tag properties.
     */
    public function hasETag() : bool
    {
        return !$this->eTagPropertyDescriptions->isEmpty();
    }
    
    /**
     * Returns a map of the key property descriptions for this entity type keyed by
     * property name.
     * 
     * @return MapInterface A map of the key property descriptions for this entity type keyed by
     *                      property name.
     */
    public function getKeyPropertyDescriptions() : MapInterface
    {
        $ownKeyPropertyDescriptions = $this->keyPropertyDescriptions;

        return $this->baseType->map(function ($baseType) use ($ownKeyPropertyDescriptions) {
            $a = new Map();

            $a->addMap($baseType->getKeyPropertyDescriptions());
            $a->addMap($ownKeyPropertyDescriptions);

            return $a;
        })->getOrElse($ownKeyPropertyDescriptions);
    }

    /**
     * Returns a map of the E-tag property descriptions for this entity type keyed by
     * property name.
     *
     * @return MapInterface A map of the E-tag property descriptions for this entity type keyed by
     *                      property name.
     */
    public function getETagPropertyDescriptions() : MapInterface
    {
        $ownETagPropertyDescriptions = $this->eTagPropertyDescriptions;

        return $this->baseType->map(function ($baseType) use ($ownETagPropertyDescriptions) {
            $a = new Map();

            $a->addMap($baseType->getETagPropertyDescriptions());
            $a->addMap($ownETagPropertyDescriptions);

            return $a;
        })->getOrElse($ownETagPropertyDescriptions);
    }

    /**
     * {@inheritDoc}
     */
    public function getStructuralPropertyDescriptions() : MapInterface
    {
        $ownStructuralPropertyDescriptions = parent::getStructuralPropertyDescriptions();

        return $this->baseType->map(function ($baseType) use ($ownStructuralPropertyDescriptions) {
            $a = new Map();

            $a->addMap($baseType->getStructuralPropertyDescriptions());
            $a->addMap($ownStructuralPropertyDescriptions);

            return $a;
        })->getOrElse($ownStructuralPropertyDescriptions);
    }
    
    /**
     * Returns a map of the navigation property descriptions for this entity type
     * keyed by property name.
     * 
     * @return MapInterface A map of the navigation property descriptions for this entity type
     *                      keyed by property name.
     */
    public function getNavigationPropertyDescriptions() : MapInterface
    {
        $ownNavigationPropertyDescriptions = $this->navigationPropertyDescriptions;

        return $this->baseType->map(function ($baseType) use ($ownNavigationPropertyDescriptions) {
            $a = new Map();

            $a->addMap($baseType->getNavigationPropertyDescriptions());
            $a->addMap($ownNavigationPropertyDescriptions);

            return $a;
        })->getOrElse($ownNavigationPropertyDescriptions);
    }
}
