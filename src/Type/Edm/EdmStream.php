<?php

declare(strict_types=1);

namespace RSSchermer\EntityModel\Type\Edm;

final class EdmStream extends AbstractEdmPrimitiveType
{
    private static $instance;

    private function __construct()
    {
    }

    public static function create()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getName() : string
    {
        return 'Stream';
    }
}
