<?php

declare(strict_types=1);

namespace RSSchermer\EntityModel;

use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;

use RSSchermer\EntityModel\Exception\InvalidArgumentException;

/**
 * Represents a data element that can be part of a schema.
 *
 * @author Roland Schermer <roland0507@gmail.com>
 */
abstract class AbstractNamedSchemaElement implements NamedSchemaConstructInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Option
     */
    private $schema;

    /**
     * Creates a new named schema element.
     *
     * @param string $name The name of the schema element (may contain only alphanumber characters
     *                     and underscores).
     *
     * @throws InvalidArgumentException Thrown if the schema element's name contains illegal characters.
     */
    public function __construct(string $name)
    {
        if (!preg_match('/^[A-Za-z0-9_]+$/', $name)) {
            throw new InvalidArgumentException(sprintf(
                '"%s" is an illegal name for a data schema element. The name for a data schema element may only ' .
                'contain alphanumeric characters and underscores.',
                $name
            ));
        }

        $this->name = $name;
        $this->schema = None::create();
    }

    /**
     * {@inheritDoc}
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Sets the schema the named schema element is defined on.
     *
     * Sets the schema the named schema element is defined on. A named schema element should
     * always be part of some schema.
     *
     * @param Schema $schema The schema the named schema element is defined on.
     */
    public function setSchema(Schema $schema)
    {
        $this->schema = new Some($schema);
    }
    /**
     * Returns the schema the named schema element is a part of.
     *
     * @return Option The schema the named schema element is a part of wrapped in Some
     *                or None if no schema is assigned yet.
     */
    public function getSchema() : Option
    {
        return $this->schema;
    }

    /**
     * {@inheritDoc}
     */
    public function getNamespace() : string
    {
        return $this->schema->map(function ($model) {
            return $model->getNamespace();
        })->getOrElse("");
    }

    /**
     * {@inheritDoc}
     */
    public function getFullName() : string
    {
        $namespace = $this->getNamespace();

        return $namespace ? $namespace .'.'. $this->getName() : $this->getName();
    }
}
