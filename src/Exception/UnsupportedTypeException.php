<?php

declare(strict_types=1);

namespace Spiral\Serializer\Symfony\Exception;

final class UnsupportedTypeException extends \InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('Symfony Serializer supports the deserialization only to objects. Provide in the 
        `$type` parameter the name of the class or object for the deserialization.');
    }
}
