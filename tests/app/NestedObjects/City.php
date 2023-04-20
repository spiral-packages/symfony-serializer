<?php

namespace Spiral\Serializer\Symfony\Tests\App\NestedObjects;

final class City implements \JsonSerializable
{
    public function __construct(
        public string $name,
        public \DateTimeZone $timezone,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'timezone' => $this->timezone->getName(),
        ];
    }
}
