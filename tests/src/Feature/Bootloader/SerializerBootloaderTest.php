<?php

declare(strict_types=1);

namespace Spiral\Serializer\Symfony\Tests\Feature\Bootloader;

use Spiral\Serializer\SerializerManager;
use Spiral\Serializer\Symfony\Config\SerializerConfig;
use Spiral\Serializer\Symfony\EncodersRegistry;
use Spiral\Serializer\Symfony\EncodersRegistryInterface;
use Spiral\Serializer\Symfony\NormalizersRegistry;
use Spiral\Serializer\Symfony\NormalizersRegistryInterface;
use Spiral\Serializer\Symfony\Encoder;
use Spiral\Serializer\Symfony\Serializer;
use Spiral\Serializer\Symfony\Tests\Feature\TestCase;

final class SerializerBootloaderTest extends TestCase
{
    public function testNormalizersRegistryInterfaceShouldBeAsSingleton(): void
    {
        $this->assertContainerBoundAsSingleton(NormalizersRegistryInterface::class, NormalizersRegistry::class);
    }

    public function testEncodersRegistryInterfaceShouldBeAsSingleton(): void
    {
        $this->assertContainerBoundAsSingleton(EncodersRegistryInterface::class, EncodersRegistry::class);
    }

    public function testDefaultConfigIsLoaded(): void
    {
        $config = $this->getConfig(SerializerConfig::CONFIG);

        $this->assertIsArray($config['normalizers']);
        $this->assertSame([], $config['normalizers']);

        $this->assertIsArray($config['encoders']);
        $this->assertSame([], $config['encoders']);
    }

    public function testSerializerIsConfigured(): void
    {
        $manager = $this->getContainer()->get(SerializerManager::class);

        $this->assertInstanceOf(Serializer::class, $manager->getSerializer('symfony-json'));
        $this->assertInstanceOf(Serializer::class, $manager->getSerializer('symfony-csv'));
        $this->assertInstanceOf(Serializer::class, $manager->getSerializer('symfony-xml'));
        $this->assertInstanceOf(Serializer::class, $manager->getSerializer('symfony-yaml'));
    }
}
