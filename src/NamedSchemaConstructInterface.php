<?php

declare(strict_types=1);

namespace RSSchermer\EntityModel;

/**
 * Represents a data element that can be part of a schema.
 *
 * @author Roland Schermer <roland0507@gmail.com>
 */
interface NamedSchemaConstructInterface
{
    /**
     * Returns the name of the named schema construct without a namespace prefix.
     *
     * @return string The name of the named schema construct.
     */
    public function getName() : string;

    /**
     * Returns the namespace of the named schema construct.
     *
     * Returns the namespace of the schema the named schema construct is a part of.
     *
     * @return string The schema construct's namespace.
     */
    public function getNamespace() : string;

    /**
     * Returns the full name of the named schema construct with namespace prefix.
     *
     * Returns the name of the named schema construct with namespace prefix if an entity data
     * model was set or the name without a prefix if no schema was set.
     *
     * @return string The full name of the named schema construct.
     */
    public function getFullName() : string;
}