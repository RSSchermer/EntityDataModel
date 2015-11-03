<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel;

/**
 * Representents a data construct in a data model.
 * 
 * @author Roland Schermer <roland0507@gmail.com>
 */
interface NamedModelConstruct
{
    /**
     * Returns the name of the named model construct without namespace prefix.
     *
     * @return string The name of the named model construct.
     */
    public function getName() : string;
    
    /**
     * Returns the namespace of the named model construct.
     * 
     * Returns the namespace of the entity data model the named model construct is a part of.
     * 
     * @return string The type's namespace.
     */
    public function getNamespace() : string;

    /**
     * Returns the full name of the named model construct with namespace prefix.
     * 
     * Returns the name of the named model element with namespace prefix if an entity data
     * model was set or the name without a prefix if no entity data model was set.
     *
     * @return string The full name of the named model element.
     */
    public function getFullName() : string;
}
