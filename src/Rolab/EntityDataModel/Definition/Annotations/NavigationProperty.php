<?php

/*
 * This file is part of the Rolab Entity Data Model library.
 *
 * (c) Roland Schermer <roland0507@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rolab\EntityDataModel\Annotations;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class NavigationProperty extends Annotation
{
    /** @var string */
    public $name;
    
    /** @var string */
    public $role;
    
    /** @var string */
    public $targetEntity;
    
    /** @var string */
    public $targetRole;
    
    /** @var string */
    public $multiplicity = '0..1';
    
    /** @var string */
    public $deleteAction = 'none';
}
