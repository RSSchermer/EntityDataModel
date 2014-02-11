<?php

/*
 * This file is part of the Rolab Entity Data Model library.
 *
 * (c) Roland Schermer <roland0507@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rolab\EntityDataModel;

use Rolab\EntityDataModel\Exception\InvalidArgumentException;

/**
 * Representents a data construct in a data model.
 * 
 * @author Roland Schermer <roland0507@gmail.com>
 */
abstract class NamedModelConstruct
{
    /**
     * @var string
     */
    private $name;
    
    /**
     * Creates a new named model construct.
     * 
     * @param string $name The name of the model construct (may contain only alphanumber characters 
     *                     and underscores).
     * 
     * @throws InvalidArgumentException Thrown if the model construct's name contains illegal characters.
     */
    public function __construct($name)
    {
        if (!preg_match('/^[A-Za-z0-9_]+$/', $name)) {
            throw new InvalidArgumentException(sprintf(
                '"%s" is an illegal name for a data model construct. The name for a data model construct may only ' .
                'contain alphanumeric characters and underscores.',
                $name
            ));
        }
        
        $this->name = $name;
    }
    
    /**
     * Returns the name of the named model construct without namespace prefix.
     *
     * @return string The name of the named model construct.
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Returns the namespace of the named model construct.
     * 
     * Returns the namespace of the entity data model the named model construct is a part of.
     * 
     * @return string The type's namespace.
     */
    abstract public function getNamespace();

    /**
     * Returns the full name of the named model construct with namespace prefix.
     * 
     * Returns the name of the named model element with namespace prefix if an entity data
     * model was set or the name without a prefix if no entity data model was set.
     *
     * @return string The full name of the named model element.
     */
    abstract public function getFullName();
}
