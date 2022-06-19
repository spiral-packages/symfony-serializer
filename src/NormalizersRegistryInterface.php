<?php

declare(strict_types=1);

namespace Spiral\Serializer\Symfony;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

interface NormalizersRegistryInterface
{
    public function register(NormalizerInterface|DenormalizerInterface $normalizer): void;

    /** @return NormalizerInterface|DenormalizerInterface[] */
    public function all(): array;

    /** @psalm-param class-string<NormalizerInterface|DenormalizerInterface> $className */
    public function has(string $className): bool;
}
