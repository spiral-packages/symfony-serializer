<?php

declare(strict_types=1);

namespace Spiral\Serializer\Symfony\Bootloader;

use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Boot\Environment\AppEnvironment;
use Spiral\Config\ConfiguratorInterface;
use Spiral\Core\Container;
use Spiral\Core\Container\Autowire;
use Spiral\Serializer\Bootloader\SerializerBootloader as SpiralSerializerBootloader;
use Spiral\Serializer\SerializerRegistryInterface;
use Spiral\Serializer\Symfony\Config\SerializerConfig;
use Spiral\Serializer\Symfony\EncodersRegistry;
use Spiral\Serializer\Symfony\EncodersRegistryInterface;
use Spiral\Serializer\Symfony\NormalizersRegistry;
use Spiral\Serializer\Symfony\NormalizersRegistryInterface;
use Spiral\Serializer\Symfony\Serializer;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
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
    ];

    protected const DEPENDENCIES = [
        SpiralSerializerBootloader::class,
    ];

    public function __construct(
        private readonly AppEnvironment $environment,
        private readonly Container $container
    ) {
    }

    public function init(ConfiguratorInterface $configs): void
    {
        $config = new SerializerConfig();

        $configs->setDefaults(SerializerConfig::CONFIG, [
            'normalizers' => $config->getDefaultNormalizers($this->environment->isProduction()),
            'encoders' => $config->getDefaultEncoders()
        ]);
    }

    public function boot(SerializerRegistryInterface $registry, SymfonySerializerInterface $serializer): void
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
        SymfonySerializerInterface $serializer
    ): void {
        $registry->register('json', new Serializer($serializer, 'json'));
        $registry->register('csv', new Serializer($serializer, 'csv'));
        $registry->register('xml', new Serializer($serializer, 'xml'));

        if (\class_exists(Dumper::class)) {
            $registry->register('yaml', new Serializer($serializer, 'yaml'));
        }
    }

    private function initNormalizersRegistry(SerializerConfig $config): NormalizersRegistryInterface
    {
        return new NormalizersRegistry(
            \array_map(static fn (mixed $normalizer) => match (true) {
                $normalizer instanceof NormalizerInterface => $normalizer,
                $normalizer instanceof DenormalizerInterface => $normalizer,
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
