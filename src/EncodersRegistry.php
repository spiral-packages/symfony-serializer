<?php

declare(strict_types=1);

namespace Spiral\Serializer\Symfony;

use Symfony\Component\Serializer\Encoder\EncoderInterface;

class EncodersRegistry implements EncodersRegistryInterface
{
    private array $encoders = [];

    public function __construct(array $encoders = [])
    {
        foreach ($encoders as $encoder) {
            $this->register($encoder);
        }
    }

    public function register(EncoderInterface $encoder): void
    {
        if (!$this->has($encoder::class)) {
            $this->encoders[] = $encoder;
        }
    }

    /** @return EncoderInterface[] */
    public function all(): array
    {
        return $this->encoders;
    }

    /** @psalm-param class-string<EncoderInterface> $className */
    public function has(string $className): bool
    {
        foreach ($this->encoders as $encoder) {
            if ($className === $encoder::class) {
                return true;
            }
        }

        return false;
    }
}
