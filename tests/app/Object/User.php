<?php

declare(strict_types=1);

namespace Spiral\Serializer\Symfony\Tests\App\Object;

class User
{
    public function __construct(
        public int $id,
        public \DateTimeInterface $registeredAt
    ) {
    }
}
