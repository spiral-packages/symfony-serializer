<?php

declare(strict_types=1);

namespace Spiral\Serializer\Symfony;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

interface NormalizersRegistryInterface
{
    public function register(NormalizerInterface $normalizer): void;

    /** @return NormalizerInterface[] */
    public function all(): array;

    /** @psalm-param class-string<NormalizerInterface> $className */
    public function has(string $className): bool;
}
