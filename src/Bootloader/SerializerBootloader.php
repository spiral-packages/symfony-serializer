<?php

declare(strict_types=1);

namespace Spiral\Serializer\Symfony\Bootloader;

use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Boot\Environment\AppEnvironment;
use Spiral\Core\Container;
use Spiral\Core\Container\Autowire;
use Spiral\Serializer\Bootloader\SerializerBootloader as SpiralSerializerBootloader;
use Spiral\Serializer\SerializerRegistry;
use Spiral\Serializer\Symfony\Config\SerializerConfig;
use Spiral\Serializer\Symfony\EncodersRegistry;
use Spiral\Serializer\Symfony\EncodersRegistryInterface;
use Spiral\Serializer\Symfony\NormalizersRegistry;
use Spiral\Serializer\Symfony\NormalizersRegistryInterface;
use Spiral\Serializer\Symfony\Serializer;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;
use Symfony\Component\Yaml\Dumper;

final class SerializerBootloader extends Bootloader
{
    protected const SINGLETONS = [
        NormalizersRegistryInterface::class => [self::class, 'initNormalizersRegistry'],
        EncodersRegistryInterface::class => [self::class, 'initEncodersRegistry'],
    ];

    protected const DEPENDENCIES = [
        SpiralSerializerBootloader::class,
    ];

    public function __construct(
        private readonly AppEnvironment $environment,
        private readonly Container $container
    ) {
    }

    public function boot(
        SerializerRegistry $registry,
        NormalizersRegistryInterface $normalizers,
        EncodersRegistryInterface $encoders
    ): void {
        $this->configureSerializer($registry, $normalizers, $encoders);
    }

    private function configureSerializer(
        SerializerRegistry $registry,
        NormalizersRegistryInterface $normalizers,
        EncodersRegistryInterface $encoders
    ): void {
        $symfonySerializer = new SymfonySerializer($normalizers->all(), $encoders->all());

        $registry->register('json', new Serializer($symfonySerializer, 'json'));
        $registry->register('csv', new Serializer($symfonySerializer, 'csv'));
        $registry->register('xml', new Serializer($symfonySerializer, 'xml'));

        if (\class_exists(Dumper::class)) {
            $registry->register('yaml', new Serializer($symfonySerializer, 'yaml'));
        }
    }

    private function initNormalizersRegistry(SerializerConfig $config): NormalizersRegistryInterface
    {
        return new NormalizersRegistry(
            \array_map(static fn (string|Autowire|NormalizerInterface $normalizer) => match (true) {
                $normalizer instanceof NormalizerInterface => $normalizer,
                $normalizer instanceof Autowire => $normalizer->resolve($this->container),
                default => $this->container->get($normalizer)
            }, $config->getNormalizers($this->environment->isProduction()))
        );
    }

    private function initEncodersRegistry(SerializerConfig $config): EncodersRegistryInterface
    {
        return new EncodersRegistry(
            \array_map(static fn (string|Autowire|EncoderInterface $encoder) => match (true) {
                $encoder instanceof EncoderInterface => $encoder,
                $encoder instanceof Autowire => $encoder->resolve($this->container),
                default => $this->container->get($encoder)
            }, $config->getEncoders())
        );
    }
}
