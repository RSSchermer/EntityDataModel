<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel;

use Rolab\EntityDataModel\Type\NavigationPropertyDescription;
use Rolab\EntityDataModel\Exception\InvalidArgumentException;

/**
 * Binds a navigation property description on an entity type to an entity set in a container.
 *
 * @author Roland Schermer <roland0507@gmail.com>
 */
class NavigationPropertyBinding
{
    /**
     * @var NavigationPropertyDescription
     */
    private $navigationPropertyDescription;

    /**
     * @var EntitySet
     */
    private $targetSet;

    /**
     * Creates a new navigation property binding.
     *
     * @param NavigationPropertyDescription $navigationPropertyDescription The navigation property this is a binding
     *                                                                     for.
     * @param EntitySet                     $targetSet                     The target entity set the navigation
     *                                                                     property is being bound to.
     *
     * @throws InvalidArgumentException Thrown if the target entity set's entity type is not a subtype of the
     *                                  navigation property's value type.
     */
    public function __construct(NavigationPropertyDescription $navigationPropertyDescription, EntitySet $targetSet)
    {
        if (!$targetSet->getEntityType()->isSubTypeOf($navigationPropertyDescription->getPropertyValueType())) {
            throw new InvalidArgumentException(sprintf(
                'Tried to bind entity set "%s", defined on entity type "%s", to navigation property "%s" with ' .
                'property value type "%s". Cannot bind a navigation property to an entity set on an entity type ' .
                'that is not a subtype of the navigation property\'s value type.',
                $targetSet->getName(),
                $targetSet->getEntityType()->getFullName(),
                $navigationPropertyDescription->getName(),
                $navigationPropertyDescription->getPropertyValueType()->getFullName()
            ));
        }

        $this->navigationPropertyDescription = $navigationPropertyDescription;
        $this->targetSet = $targetSet;
    }

    /**
     * Returns the navigation property description this is a binding for.
     *
     * @return NavigationPropertyDescription The navigation property description this is a binding for.
     */
    public function getNavigationPropertyDescription() : NavigationPropertyDescription
    {
        return $this->navigationPropertyDescription;
    }

    /**
     * The target entity set for this navigation property binding.
     *
     * @return EntitySet The target entity set for this navigation property binding.
     */
    public function getTargetSet() : EntitySet
    {
        return $this->targetSet;
    }
}
