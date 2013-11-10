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

abstract class PropertyDefinition
{
    private $name;

    private $isCollection;

    public function __construct($name, $isCollection = false)
    {
        $this->name = $name;
        $this->isCollection = $isCollection;
    }

    public function getName()
    {
        return $this->name;
    }

    public function isCollection()
    {
        return (bool) $this->isCollection;
    }
}
