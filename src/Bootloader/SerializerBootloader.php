<?php

declare(strict_types=1);

namespace Spiral\Serializer\Symfony\Bootloader;

use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Config\ConfiguratorInterface;
use Spiral\Core\Container;
use Spiral\Core\Container\Autowire;
use Spiral\Core\FactoryInterface;
use Spiral\Serializer\Bootloader\SerializerBootloader as SpiralSerializerBootloader;
use Spiral\Serializer\SerializerRegistryInterface;
use Spiral\Serializer\Symfony\Config\SerializerConfig;
use Spiral\Serializer\Symfony\EncodersRegistry;
use Spiral\Serializer\Symfony\EncodersRegistryInterface;
use Spiral\Serializer\Symfony\NormalizersRegistry;
use Spiral\Serializer\Symfony\NormalizersRegistryInterface;
use Spiral\Serializer\Symfony\Serializer;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Mapping\Loader\LoaderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;
use Symfony\Component\Serializer\SerializerInterface as SymfonySerializerInterface;
use Symfony\Component\Yaml\Dumper;

final class SerializerBootloader extends Bootloader
{
    protected const SINGLETONS = [
        NormalizersRegistryInterface::class => [self::class, 'initNormalizersRegistry'],
        EncodersRegistryInterface::class => [self::class, 'initEncodersRegistry'],
        SymfonySerializerInterface::class => [self::class, 'initSymfonySerializer'],
        LoaderInterface::class => [self::class, 'initMappingLoader'],
        SymfonySerializer::class => SymfonySerializerInterface::class,
    ];

    protected const DEPENDENCIES = [
        SpiralSerializerBootloader::class,
    ];

    public function __construct(
        private readonly Container $container
    ) {
    }

    public function init(ConfiguratorInterface $configs): void
    {
        $configs->setDefaults(SerializerConfig::CONFIG, [
            'normalizers' => [],
            'encoders' => []
        ]);
    }

    public function boot(SerializerRegistryInterface $registry, SymfonySerializer $serializer): void
    {
        $this->configureSerializer($registry, $serializer);
    }

    private function initSymfonySerializer(
        NormalizersRegistryInterface $normalizers,
        EncodersRegistryInterface $encoders
    ): SymfonySerializer {
        return new SymfonySerializer($normalizers->all(), $encoders->all());
    }

    private function configureSerializer(
        SerializerRegistryInterface $registry,
        SymfonySerializer $serializer
    ): void {
        $registry->register('symfony-json', new Serializer($serializer, 'json'));
        $registry->register('symfony-csv', new Serializer($serializer, 'csv'));
        $registry->register('symfony-xml', new Serializer($serializer, 'xml'));

        if (\class_exists(Dumper::class)) {
            $registry->register('symfony-yaml', new Serializer($serializer, 'yaml'));
        }
    }

    private function initNormalizersRegistry(
        SerializerConfig $config,
        FactoryInterface $factory
    ): NormalizersRegistryInterface {
        $normalizers = \array_map(fn (mixed $normalizer) => match (true) {
            $normalizer instanceof NormalizerInterface => $normalizer,
            $normalizer instanceof DenormalizerInterface => $normalizer,
            default => $this->container->get($normalizer)
        }, $config->getNormalizers());

        return $factory->make(NormalizersRegistry::class, ['normalizers' => $normalizers]);
    }

    private function initEncodersRegistry(SerializerConfig $config): EncodersRegistryInterface
    {
        return new EncodersRegistry(
            \array_map(fn (string|Autowire|EncoderInterface $encoder) =>
                $encoder instanceof EncoderInterface ? $encoder : $this->container->get($encoder),
                $config->getEncoders())
        );
    }

    private function initMappingLoader(SerializerConfig $config): LoaderInterface
    {
        return $config->getMetadataLoader();
    }
}
