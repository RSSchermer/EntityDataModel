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

use Rolab\EntityDataModel\Association;
use Rolab\EntityDataModel\AssociationSetEnd;
use Rolab\EntityDataModel\EntityContainer;
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
class AssociationSet
{
    /**
     * @var string
     */
    private $name;

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
     * @var EntityContainer
     */
    private $entityContainer;
    
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
    public function __construct($name, Association $association, AssociationSetEnd $setEndOne = null,
        AssociationSetEnd $setEndTwo = null
    ) {
        if (!preg_match('/^[A-Za-z0-9_]+$/', $name)) {
            throw new InvalidArgumentException(sprintf('"%s" is an illegal name for an association set. The name for ' .
                'an association set may only contain alphanumeric characters and underscores.', $name));
        }

        $this->name = $name;
        $this->association = $association;
        $this->setEndOne = $setEndOne;
        $this->setEndTwo = $setEndTwo;
    }
    
    /**
     * Sets the entity container the association set is contained in.
     * 
     * Sets the entity container the association set is contained in. An association set should
     * always be part of some entity container.
     * 
     * @param EntityContainer $entityContainer The entity container the association set is a part of.
     */
    public function setEntityContainer(EntityContainer $entityContainer)
    {
        $this->entityContainer = $entityContainer;
    }
    
    /**
     * Returns the entity container the association set is contained in.
     * 
     * @return null|EntityContainer The entity container the association set is contained in or null if
     *                              no entity container was set.
     */
    public function getEntityContainer()
    {
        return $this->entityContainer;
    }
    
    /**
     * Returns the name of the association set.
     * 
     * @return string The name of the association set.
     */
    public function getName()
    {
        return $this->name;
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
     * @return array An array containing the set ends.
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
