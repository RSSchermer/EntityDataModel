<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel\Type;

use Rolab\EntityDataModel\Exception\InvalidArgumentException;
use Rolab\EntityDataModel\Exception\RuntimeException;

/**
 * Describes a navigation property on an entity type.
 *
 * Navigation property's define a relationship from the navigation property's
 * owner entity type to the navigation property's target entity type. A partner
 * property may optionally be specified, which defines is a navigation property
 * on the target entity type that describes the inverse relationship back to
 * this navigation property's owner entity type.
 * 
 * @author Roland Schermer <roland0507@gmail.com>
 */
class NavigationPropertyDescription extends ResourcePropertyDescription
{
    const DELETE_ACTION_NONE = 'none';

    const DELETE_ACTION_CASCADE = 'cascade';

    const DELETE_ACTION_SET_NULL = 'set_null';

    const DELETE_ACTION_SET_DEFAULT = 'set_default';

    /**
     * @var NavigationPropertyDescription
     */
    private $partner;

    /**
     * @var integer
     */
    private $onDeleteAction;

    /**
     * Creates a new resource property description.
     *
     * @param string              $name              The name of the navigation property description. (may
     *                                               only consist of alphanumeric characters and the
     *                                               underscore).
     * @param \ReflectionProperty $reflection        A reflection object for the property being described.
     * @param EntityType          $propertyValueType The type of the property value.
     * @param boolean             $isCollection      Whether or not the property value is a collection.
     * @param boolean             $nullable          Whether or not the property is nullable.
     * @param integer             $onDeleteAction    Valid values are NavigationPropertyDescription::ON_DELETE_NONE,
     *                                               NavigationPropertyDescription::ON_DELETE_CASCADE,
     *                                               NavigationPropertyDescription::ON_DELETE_SET_NULL and
     *                                               NavigationPropertyDescription::ON_DELETE_SET_DEFAULT.
     *
     * @throws InvalidArgumentException Thrown if the name contains illegal characters.
     * @throws InvalidArgumentException Thrown if the "on delete" action is invalid. Valid values are
     *                                  NavigationPropertyDescription::ON_DELETE_NONE,
     *                                  NavigationPropertyDescription::ON_DELETE_CASCADE,
     *                                  NavigationPropertyDescription::ON_DELETE_SET_NULL and
     *                                  NavigationPropertyDescription::ON_DELETE_SET_DEFAULT.
     */
    public function __construct(
        string $name,
        \ReflectionProperty $reflection,
        EntityType $propertyValueType,
        bool $isCollection = false,
        bool $nullable = true,
        string $onDeleteAction = self::DELETE_ACTION_NONE
    ) {
        parent::__construct($name, $reflection, $propertyValueType, $isCollection, $nullable);

        if (!in_array($onDeleteAction, array(
            self::DELETE_ACTION_NONE,
            self::DELETE_ACTION_CASCADE,
            self::DELETE_ACTION_SET_NULL,
            self::DELETE_ACTION_SET_DEFAULT
        ))) {
            throw new InvalidArgumentException(sprintf(
                'Tried to set "%s" as the value for the "on delete" action on navigation property "%s". Valid values ' .
                'are %s for no delete action, %s for cascading the delete, %s for setting the property to null and ' .
                '%s for setting the property to its default value.',
                $onDeleteAction,
                $this->getName(),
                self::DELETE_ACTION_NONE,
                self::DELETE_ACTION_CASCADE,
                self::DELETE_ACTION_SET_NULL,
                self::DELETE_ACTION_SET_DEFAULT
            ));
        }

        $this->onDeleteAction = $onDeleteAction;
    }

    /**
     * Set this navigation property's partner property.
     *
     * A navigation property's partner property is a navigation property on this
     * navigation property's target entity type that defines the inverse navigation,
     * back to this navigation property's own entity type.
     *
     * @param NavigationPropertyDescription $partner The partner navigation property.
     *
     * @throws InvalidArgumentException If the partner property's owner entity type is not a
     *                                  subtype if this navigation property value type.
     * @throws InvalidArgumentException If this navigation property's owner entity type is not a
     *                                  subtype of the partner property's value type.
     */
    public function setPartner(NavigationPropertyDescription $partner)
    {
        if (null === $this->getStructuredType()) {
            throw new RuntimeException(
                'Cannot call `setPartner` on a navigation property for which an owner entity type has not yet been ' .
                'specified. Call `setStructuredType` to set an entity type that owns the navigation property ' .
                'before calling `setPartner`.'
            );
        }

        if (!$partner->getStructuredType()->isSubTypeOf($this->getPropertyValueType())) {
            throw new InvalidArgumentException(sprintf(
                'Tried to set property "%s" on entity type "%s" as the partner of navigation property "%s" with ' .
                'value type "%s". The entity type on which a navigation property\'s partner property is defined, ' .
                'must be a subtype of this navigation property\'s own value type.',
                $partner->getName(),
                $partner->getStructuredType()->getFullName(),
                $this->getName(),
                $this->getPropertyValueType()->getFullName()
            ));
        }

        if (!$this->getStructuredType()->isSubTypeOf($partner->getPropertyValueType())) {
            throw new InvalidArgumentException(sprintf(
                'Tried to set property "%s" with value type "%s" as the partner of navigation property "%s" on ' .
                'entity type "%s". The entity type on which this navigation property is defined, must be a subtype ' .
                'the partner property\'s value type.',
                $partner->getName(),
                $partner->getPropertyValueType()->getFullName(),
                $this->getName(),
                $this->getStructuredType()->getFullName()
            ));
        }

        $this->partner = $partner;
    }

    /**
     * Set this navigation property's partner property.
     *
     * A navigation property's partner property is a navigation property on this
     * navigation property's target entity type that defines the inverse navigation,
     * back to this navigation property's own entity type.
     *
     * @return NavigationPropertyDescription|null This navigation property's partner property.
     */
    public function getPartner()
    {
        return $this->partner;
    }

    /**
     * Returns an integer indicating this navigation property's "on delete" action.
     *
     * A value of 0 indicates no delete action, a value of 1 indicates a cascading delete action,
     * a value of 2 indicates that the delete action will set the partner navigation property to
     * null, a value of 3 that the delete action will set the partner navigation property to its
     * default value.
     *
     * @return int An integer indicating this navigation property's "on delete" action.
     */
    public function getOnDeleteAction() : string
    {
        return $this->onDeleteAction;
    }
}
