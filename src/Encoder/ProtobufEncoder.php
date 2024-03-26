<?php

declare(strict_types=1);

namespace Spiral\Serializer\Symfony\Encoder;

use Google\Protobuf\Internal\Message;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Exception\RuntimeException;

/**
 * This is a proxy class to the ProtobufNormalizer. All the logic of encoding and decoding is in the ProtobufNormalizer.
 */
final class ProtobufEncoder implements EncoderInterface, DecoderInterface
{
    public const FORMAT = 'protobuf';
    private const ALTERNATIVE_FORMAT = 'proto';

    public function __construct()
    {
        if (!\class_exists(Message::class)) {
            throw new RuntimeException('Package `google/protobuf` is not installed.');
        }
    }

    public function decode(string $data, string $format, array $context = []): mixed
    {
        // All the logic is in the ProtobufNormalizer
        return $data;
    }

    public function encode(mixed $data, string $format, array $context = []): string
    {
        // All the logic is in the ProtobufNormalizer
        return $data;
    }

    public function supportsDecoding(string $format): bool
    {
        return self::FORMAT === $format || self::ALTERNATIVE_FORMAT === $format;
    }

    public function supportsEncoding(string $format): bool
    {
        return $this->supportsDecoding($format);
    }
}
