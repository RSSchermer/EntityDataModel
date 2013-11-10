<?php

/*
 * This file is part of the Rolab Entity Data Model library.
 *
 * (c) Roland Schermer <roland0507@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rolab\EntityDataModel\Builder;

use Rolab\EntityDataModel\Builder\ComplexTypeDefinition;
use Rolab\EntityDataModel\Builder\KeyPropertyDefinition;
use Rolab\EntityDataModel\Builder\ETagPropertyDefinition;
use Rolab\EntityDataModel\Builder\NavigationPropertyDefinition;

class EntityTypeDefinition extends ComplexTypeDefinition
{
    private $navigationPropertyDefinitions = array();

    public function keyProperty($name, $typeName)
    {
        $this->addRegularPropertyDefinition(new KeyPropertyDefinition($name, $typeName));
    }

    public function eTagProperty($name, $typeName)
    {
        $this->addRegularPropertyDefinition(new ETagPropertyDefinition($name, $typeName));
    }

    public function navigationProperty($name, $setName, $isCollection = false)
    {
        $this->addNavigationPropertyDefinition(new NavigationPropertyDefinition($name, $setName, $isCollection));
    }

    public function addNavigationPropertyDefinition(NavigationPropertyDefinition $navigationPropertyDefinition)
    {
        $this->navigationPropertyDefinitions[] = $navigationPropertyDefinition;
    }

    public function getPropertyDefinitions()
    {
        return array_merge($this->getRegularPropertyDefinitions(), $this->getNavigationPropertyDefinitions());
    }

    public function getNavigationPropertyDefinitions()
    {
        return $this->navigationPropertyDefinitions;
    }
}
