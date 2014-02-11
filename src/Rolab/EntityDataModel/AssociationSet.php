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

use Rolab\EntityDataModel\NamedContainerElement;
use Rolab\EntityDataModel\Association;
use Rolab\EntityDataModel\AssociationSetEnd;
use Rolab\EntityDataModel\Exception\InvalidArgumentException;

/**
 * Represents an association between two entity sets. An association describes a
 * relation between different entity types. An association set represents this
 * relation for actual collections of instances: entity sets. Set ends link an
 * entity set to an association end and both set ends should be in the same
 * entity container (or one if its parent containers) as the association set.
 * 
 * @author Roland Schermer <roland0507@gmail.com>
 */
class AssociationSet extends NamedContainerElement
{
    /**
     * @var Association
     */
    private $association;

    /**
     * @var AssociationSetEnd
     */
    private $setEndOne;

    /**
     * @var AssociationSetEnd
     */
    private $setEndTwo;

    /**
     * Creates a new association set.
     * 
     * @param string                 $name        The name of the entity container (must
     *                                            contain only alphanumber characters and underscores).
     * @param Association            $association The association represented by the association set.
     * @param null|AssociationSetEnd $setEndOne   The first association set end.
     * @param null|AssociationSetEnd $setEndTwo   The second association set end.
     * 
     * @throws InvalidArgumentException Thrown if the name of the association set contains illegal 
     *                                  characters.
     */
    public function __construct(
        $name,
        Association $association,
        AssociationSetEnd $setEndOne = null,
        AssociationSetEnd $setEndTwo = null
    ) {
        parent::__construct($name);
        
        $this->association = $association;
        $this->setEndOne = $setEndOne;
        $this->setEndTwo = $setEndTwo;
    }
    
    /**
     * Returns the association the association set represents.
     * 
     * @return Association The association represented by the association.
     */
    public function getAssociation()
    {
        return $this->association;
    }
    
    /**
     * Returns the set ends of the association set.
     * 
     * @return AssociationSetEnd[] An array containing the set ends.
     */
    public function getSetEnds()
    {
        $setEnds = array();

        if (null !== $this->setEndOne) {
            $setEnds[] = $this->setEndOne;
        }

        if (null !== $this->setEndTwo) {
            $setEnds[] = $this->setEndTwo;
        }

        return $setEnds;
    }
    
    /**
     * Returns a set end based on the role name of the association end represented
     * by the association set end.
     * 
     * @return null|AssociationSetEnd The association set end that represents an association
     *                                end with the role searched for or null if no such
     *                                association set end exists.
     */
    public function getSetEndByRole($role)
    {
        if (null !== $this->setEndOne && $this->setEndOne->getAssociationEnd()->getRole() === $role) {
            return $this->setEndOne;
        } elseif (null !== $this->setEndTwo && $this->setEndTwo->getAssociationEnd()->getRole() === $role) {
            return $this->setEndTwo;
        }

        return null;
    }
}
