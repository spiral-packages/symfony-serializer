<?php

declare(strict_types=1);

namespace Spiral\Serializer\Symfony\Config;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Messenger\Transport\Serialization\Normalizer\FlattenExceptionNormalizer;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
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
        if (!empty($this->config['metadataLoader'])) {
            return $this->config['metadataLoader'];
        }

        if (\class_exists(AttributeLoader::class)) {
            return new AttributeLoader();
        }

        if (\class_exists(AnnotationLoader::class)) {
            return new AnnotationLoader(new AnnotationReader());
        }

        throw new \LogicException('No metadata loader found');
    }
}
