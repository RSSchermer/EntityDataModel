<?php

/*
 * This file is part of the Rolab Entity Data Model library.
 *
 * (c) Roland Schermer <roland0507@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rolab\EntityDataModel\Type\PropertyDescription;

use Rolab\EntityDataModel\Type\PropertyDescription\ResourcePropertyDescription;
use Rolab\EntityDataModel\Type\ResourceType;
use Rolab\EntityDataModel\Type\PropertyDescription\Facet\PropertyFacet;

abstract class RegularPropertyDescription extends ResourcePropertyDescription
{
    private $resourceType;
    
    private $isCollection;
    
    private $facets = array();
    
    public function __construct($name, \ReflectionProperty $reflection, ResourceType $resourceType, $isCollection = false)
    {
        parent::__construct($name, $reflection);
        
        $this->resourceType = $resourceType;
        $this->isCollection = $isCollection;
    }
    
    public function getResourceType()
    {
        return $this->resourceType;
    }
    
    public function isCollection()
    {
        return (bool) $this->isCollection;
    }
    
    public function addFacet(PropertyFacet $facet)
    {
        $this->facets[] = $facet;
    }
    
    public function getFacets()
    {
        return $this->facets;
    }
}
