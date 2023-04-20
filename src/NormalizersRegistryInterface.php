<?php

declare(strict_types=1);

namespace Spiral\Serializer\Symfony;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

interface NormalizersRegistryInterface
{
    /**
     * @param int<0, max> $priority
     */
    public function register(NormalizerInterface|DenormalizerInterface $normalizer, int $priority = 0): void;

    public function all(): array;

    /** @psalm-param class-string $className */
    public function has(string $className): bool;
}
