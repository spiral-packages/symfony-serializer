<?php

namespace Spiral\Serializer\Symfony\Tests\App\NestedObjects;

final class Country
{
    /**
     * @param non-empty-string $name
     * @param City[] $cities
     */
    public function __construct(
        public string $name,
        public array $cities,
    ) {
    }
}
