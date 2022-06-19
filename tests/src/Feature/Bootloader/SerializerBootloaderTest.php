<?php

declare(strict_types=1);

namespace Spiral\Serializer\Symfony\Tests\Feature\Bootloader;

use Spiral\Serializer\SerializerManager;
use Spiral\Serializer\Symfony\Config\SerializerConfig;
use Spiral\Serializer\Symfony\EncodersRegistry;
use Spiral\Serializer\Symfony\EncodersRegistryInterface;
use Spiral\Serializer\Symfony\NormalizersRegistry;
use Spiral\Serializer\Symfony\NormalizersRegistryInterface;
use Spiral\Serializer\Symfony\Serializer;
use Spiral\Serializer\Symfony\Tests\Feature\TestCase;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

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
        $this->assertCount(14, $config['normalizers']);
        foreach ($config['normalizers'] as $normalizer) {
            $this->assertTrue($normalizer instanceof NormalizerInterface || $normalizer instanceof DenormalizerInterface);
        }

        $this->assertIsArray($config['encoders']);
        $this->assertCount(4, $config['encoders']);
        foreach ($config['encoders'] as $encoder) {
            $this->assertInstanceOf(EncoderInterface::class, $encoder);
        }
    }

    public function testSerializerIsConfigured(): void
    {
        $manager = $this->getContainer()->get(SerializerManager::class);

        $this->assertInstanceOf(Serializer::class, $manager->getSerializer('json'));
        $this->assertInstanceOf(Serializer::class, $manager->getSerializer('csv'));
        $this->assertInstanceOf(Serializer::class, $manager->getSerializer('xml'));
        $this->assertInstanceOf(Serializer::class, $manager->getSerializer('yaml'));
    }
}
