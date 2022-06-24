<?php

declare(strict_types=1);

namespace Spiral\Serializer\Symfony\Exception;

final class UnsupportedTypeException extends \InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('The Symfony Serializer only supports deserialization to a specific type. Parameter 
        `$type` is required.');
    }
}
