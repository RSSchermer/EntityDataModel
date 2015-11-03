<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel;

use Rolab\EntityDataModel\Type\NavigationPropertyDescription;

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

    public function __construct(NavigationPropertyDescription $navigationPropertyDescription, EntitySet $targetSet)
    {
        $this->navigationPropertyDescription = $navigationPropertyDescription;
        $this->targetSet = $targetSet;
    }

    public function getNavigationPropertyDescription() : NavigationPropertyDescription
    {
        return $this->navigationPropertyDescription;
    }

    public function getTargetSet() : EntitySet
    {
        return $this->targetSet;
    }
}
