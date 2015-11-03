<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel\Type;

use Rolab\EntityDataModel\Exception\InvalidArgumentException;

/**
 * Describes a navigation property of an entity type.
 * 
 * @author Roland Schermer <roland0507@gmail.com>
 */
class NavigationPropertyDescription extends ResourcePropertyDescription
{
    const DELETE_ACTION_NONE = 0;

    const DELETE_ACTION_CASCADE = 1;

    const DELETE_ACTION_SET_NULL = 2;

    const DELETE_ACTION_SET_DEFAULT = 3;

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
     * @param string              $name              The name of the structural property description. (may
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
     */
    public function __construct(
        string $name,
        \ReflectionProperty $reflection,
        EntityType $propertyValueType,
        bool $isCollection = false,
        bool $nullable = true,
        int $onDeleteAction = self::DELETE_ACTION_NONE
    ) {
        parent::__construct($name, $reflection, $propertyValueType, $isCollection, $nullable);

        $this->setOnDeleteAction($onDeleteAction);
    }

    public function setPartner(NavigationPropertyDescription $partner)
    {
        if ($partner->getStructuredType() !== $this->getPropertyValueType()) {
            throw new InvalidArgumentException(
                'A navigation property\'s partner must be a declared on the entity type that is the target' .
                'of this navigation property.'
            );
        }

        $this->partner = $partner;
    }

    public function getPartner()
    {
        return $this->partner;
    }

    public function setOnDeleteAction($onDeleteAction)
    {
        if (!in_array($onDeleteAction, array(
            self::DELETE_ACTION_NONE,
            self::DELETE_ACTION_CASCADE,
            self::DELETE_ACTION_SET_NULL,
            self::DELETE_ACTION_SET_DEFAULT
        ))) {
            throw new InvalidArgumentException(sprintf(
                '"%s" is an illegal value for the "on delete" action. Valid values are %s for no delete action, ' .
                '%s for cascading the delete, %s for setting the property to null and %s for setting the ' .
                'property to its default value.',
                $onDeleteAction,
                self::DELETE_ACTION_NONE,
                self::DELETE_ACTION_CASCADE,
                self::DELETE_ACTION_SET_NULL,
                self::DELETE_ACTION_SET_DEFAULT
            ));
        }

        $this->onDeleteAction = $onDeleteAction;
    }

    public function getOnDeleteAction() : int
    {
        return $this->onDeleteAction;
    }
}
