<?php

declare(strict_types=1);

namespace Spiral\Serializer\Symfony;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class NormalizersRegistry implements NormalizersRegistryInterface
{
    private array $normalizers = [];

    public function __construct(array $normalizers = [])
    {
        foreach ($normalizers as $normalizer) {
            $this->register($normalizer);
        }
    }

    public function register(NormalizerInterface|DenormalizerInterface $normalizer): void
    {
        if (!$this->has($normalizer::class)) {
            $this->normalizers[] = $normalizer;
        }
    }

    public function all(): array
    {
        return $this->normalizers;
    }

    /** @psalm-param class-string $className */
    public function has(string $className): bool
    {
        foreach ($this->normalizers as $normalizer) {
            if ($className === $normalizer::class) {
                return true;
            }
        }

        return false;
    }
}
