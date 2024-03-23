<?php

declare(strict_types=1);

namespace Spiral\Serializer\Symfony\Normalizer;

use Google\Protobuf\Internal\Message;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Exception\RuntimeException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ProtobufMessageNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function __construct()
    {
        if (!\class_exists(Message::class)) {
            throw new RuntimeException('Package `google/protobuf` is not installed.');
        }
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        $object = new $type();

        try {
            $object->mergeFromString($data);
        } catch (\Throwable $e) {
            throw new NotNormalizableValueException(
                \sprintf('The data is not a valid "%s" protobuf binary representation.', $type),
            );
        }
        return $object;
    }

    public function supportsDenormalization(
        mixed $data,
        string $type,
        ?string $format = null,
        array $context = [],
    ): bool {
        return \is_string($data) && \is_a($type, Message::class, true);
    }

    public function normalize(
        mixed $object,
        ?string $format = null,
        array $context = [],
    ): array|string|int|float|bool|\ArrayObject|null {
        return $object->serializeToString();
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Message;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Message::class => true,
        ];
    }
}
