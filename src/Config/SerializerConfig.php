<?php

declare(strict_types=1);

namespace Spiral\Serializer\Symfony\Config;

use Symfony\Component\Messenger\Transport\Serialization\Normalizer\FlattenExceptionNormalizer;
use Symfony\Component\Serializer\Encoder;
use Symfony\Component\Serializer\Normalizer;
use Spiral\Core\InjectableConfig;

final class SerializerConfig extends InjectableConfig
{
    public const CONFIG = 'symfony-serializer';

    protected array $config = [
        'normalizers' => [],
        'encoders' => [],
    ];

    /**
     * Get registered encoders.
     */
    public function getEncoders(): array
    {
        return $this->config['encoders'] === [] ? $this->getDefaultEncoders() : $this->config['encoders'];
    }

    /**
     * Get registered normalizers.
     */
    public function getNormalizers(bool $isProduction): array
    {
        return $this->config['normalizers'] === [] ?
            $this->getDefaultNormalizers($isProduction) :
            $this->config['normalizers'];
    }

    public function getDefaultEncoders(): array
    {
        return [
            new Encoder\JsonEncoder(),
            new Encoder\CsvEncoder(),
            new Encoder\XmlEncoder(),
            new Encoder\YamlEncoder(),
        ];
    }

    public function getDefaultNormalizers(bool $isProduction): array
    {
        $normalizers = [
            new Normalizer\UnwrappingDenormalizer(),
            new Normalizer\ProblemNormalizer(debug: $isProduction === false),
            new Normalizer\UidNormalizer(),
            new Normalizer\JsonSerializableNormalizer(),
            new Normalizer\DateTimeNormalizer(),
            new Normalizer\ConstraintViolationListNormalizer(),
            new Normalizer\MimeMessageNormalizer(new Normalizer\PropertyNormalizer()),
            new Normalizer\DateTimeZoneNormalizer(),
            new Normalizer\DateIntervalNormalizer(),
            new Normalizer\FormErrorNormalizer(),
            new Normalizer\BackedEnumNormalizer(),
            new Normalizer\DataUriNormalizer(),
            new Normalizer\ArrayDenormalizer(),
            new Normalizer\ObjectNormalizer()
        ];

        // Normalizer from symfony/messenger, if exists
        if (\class_exists(FlattenExceptionNormalizer::class)) {
            $normalizers[] = new FlattenExceptionNormalizer();
        }

        return $normalizers;
    }
}
