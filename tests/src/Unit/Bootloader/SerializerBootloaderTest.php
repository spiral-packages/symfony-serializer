<?php

declare(strict_types=1);

namespace Spiral\Serializer\Symfony\Tests\Unit\Bootloader;

use Spiral\Boot\Environment\AppEnvironment;
use Spiral\Core\Container;
use Spiral\Core\Container\Autowire;
use Spiral\Serializer\Symfony\Bootloader\SerializerBootloader;
use Spiral\Serializer\Symfony\Config\SerializerConfig;
use Spiral\Serializer\Symfony\EncodersRegistry;
use Spiral\Serializer\Symfony\NormalizersRegistry;
use Spiral\Serializer\Symfony\Tests\Unit\TestCase;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Normalizer\UidNormalizer;
use Symfony\Component\Serializer\Normalizer\UnwrappingDenormalizer;

final class SerializerBootloaderTest extends TestCase
{
    public function testConfigureEncoders(): void
    {
        $bootloader = new SerializerBootloader(AppEnvironment::Local, new Container());

        $ref = new \ReflectionMethod($bootloader, 'initEncodersRegistry');

        /** @var EncodersRegistry $registry */
        $registry = $ref->invoke($bootloader, new SerializerConfig([
            'encoders' => [
                JsonEncoder::class,
                new CsvEncoder(),
                new Autowire(XmlEncoder::class)
            ]
        ]));

        $this->assertCount(3, $registry->all());
        $this->assertTrue($registry->has(JsonEncoder::class));
        $this->assertTrue($registry->has(CsvEncoder::class));
        $this->assertTrue($registry->has(XmlEncoder::class));
    }

    public function testConfigureNormalizers(): void
    {
        $bootloader = new SerializerBootloader(AppEnvironment::Local, new Container());

        $ref = new \ReflectionMethod($bootloader, 'initNormalizersRegistry');

        /** @var NormalizersRegistry $registry */
        $registry = $ref->invoke($bootloader, new SerializerConfig([
            'normalizers' => [
                UnwrappingDenormalizer::class,
                new UidNormalizer(),
                new Autowire(JsonSerializableNormalizer::class)
            ]
        ]));

        $this->assertCount(3, $registry->all());
        $this->assertTrue($registry->has(UnwrappingDenormalizer::class));
        $this->assertTrue($registry->has(UidNormalizer::class));
        $this->assertTrue($registry->has(JsonSerializableNormalizer::class));
    }
}
