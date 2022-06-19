<?php

namespace Spiral\SymfonySerializer\Tests;

use Spiral\Boot\Bootloader\ConfigurationBootloader;
use Spiral\Serializer\Symfony\Bootloader\SerializerBootloader;

class TestCase extends \Spiral\Testing\TestCase
{
    public function rootDirectory(): string
    {
        return __DIR__.'/../';
    }

    public function defineBootloaders(): array
    {
        return [
            ConfigurationBootloader::class,
            SerializerBootloader::class,
            // ...
        ];
    }
}
