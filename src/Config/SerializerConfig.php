<?php

declare(strict_types=1);

namespace Spiral\Serializer\Symfony\Config;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Messenger\Transport\Serialization\Normalizer\FlattenExceptionNormalizer;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Mapping\Loader\LoaderInterface;
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
        return $this->config['encoders'] ?? [];
    }

    /**
     * Get registered normalizers.
     */
    public function getNormalizers(): array
    {
        return $this->config['normalizers'] ?? [];
    }

    public function getMetadataLoader(): LoaderInterface
    {
        return $this->config['metadataLoader'] ?? new AnnotationLoader(new AnnotationReader());
    }
}
