<?php

declare(strict_types=1);

namespace Spiral\Serializer\Symfony;

use Spiral\Serializer\SerializerInterface;
use Spiral\Serializer\Symfony\Exception\UnsupportedTypeException;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;

class Serializer implements SerializerInterface
{
    public function __construct(
        private readonly SymfonySerializer $serializer,
        private readonly string $format
    ) {
    }

    public function serialize(mixed $payload, array $context = []): string
    {
        return $this->serializer->serialize($payload, $this->format);
    }

    public function unserialize(
        \Stringable|string $payload,
        object|string|null $type = null,
        array $context = []
    ): mixed {
        if ($type === null) {
            throw new UnsupportedTypeException();
        }

        return $this->serializer->deserialize(
            (string) $payload,
            \is_object($type) ? $type::class : $type,
            $this->format,
            $context
        );
    }

    public function normalize(
        mixed $data,
        string $format = null,
        array $context = []
    ): array|string|int|float|bool|\ArrayObject|null {
        return $this->serializer->normalize($data, $format, $context);
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): mixed
    {
        return $this->serializer->denormalize($data, $type, $format, $context);
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return $this->serializer->supportsNormalization($data, $format, $context);
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return $this->serializer->supportsDenormalization($data, $type, $format, $context);
    }

    public function encode(mixed $data, string $format, array $context = []): string
    {
        return $this->serializer->encode($data, $format, $context);
    }

    public function decode(string $data, string $format, array $context = []): mixed
    {
        return $this->serializer->decode($data, $format, $context);
    }

    public function supportsEncoding(string $format, array $context = []): bool
    {
        return $this->serializer->supportsEncoding($format, $context);
    }

    public function supportsDecoding(string $format, array $context = []): bool
    {
        return $this->serializer->supportsDecoding($format, $context);
    }
}
