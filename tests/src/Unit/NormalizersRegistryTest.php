<?php

declare(strict_types=1);

namespace Spiral\Serializer\Symfony\Tests\Unit;

use Spiral\Boot\Environment\DebugMode;
use Spiral\Serializer\Symfony\Normalizer\RamseyUuidNormalizer;
use Spiral\Serializer\Symfony\NormalizersRegistry;
use Symfony\Component\Serializer\Mapping\Loader\LoaderInterface;
use Symfony\Component\Serializer\Normalizer;

final class NormalizersRegistryTest extends TestCase
{
    public function testConstructWithDefaultNormalizers(): void
    {
        $registry = new NormalizersRegistry(
            $this->createMock(LoaderInterface::class),
            DebugMode::Enabled
        );

        $this->assertCount(16, $registry->all());

        $this->assertTrue($registry->has(Normalizer\UnwrappingDenormalizer::class));
        $this->assertTrue($registry->has(Normalizer\ProblemNormalizer::class));
        $this->assertTrue($registry->has(Normalizer\UidNormalizer::class));
        $this->assertTrue($registry->has(Normalizer\JsonSerializableNormalizer::class));
        $this->assertTrue($registry->has(Normalizer\DateTimeNormalizer::class));
        $this->assertTrue($registry->has(Normalizer\ConstraintViolationListNormalizer::class));
        $this->assertTrue($registry->has(Normalizer\MimeMessageNormalizer::class));
        $this->assertTrue($registry->has(Normalizer\DateTimeZoneNormalizer::class));
        $this->assertTrue($registry->has(Normalizer\DateIntervalNormalizer::class));
        $this->assertTrue($registry->has(Normalizer\FormErrorNormalizer::class));
        $this->assertTrue($registry->has(Normalizer\BackedEnumNormalizer::class));
        $this->assertTrue($registry->has(Normalizer\DataUriNormalizer::class));
        $this->assertTrue($registry->has(Normalizer\ArrayDenormalizer::class));
        $this->assertTrue($registry->has(Normalizer\ObjectNormalizer::class));
        $this->assertTrue($registry->has(RamseyUuidNormalizer::class));
    }

    public function testRegister(): void
    {
        $registry = new NormalizersRegistry(
            $this->createMock(LoaderInterface::class),
            DebugMode::Enabled
        );

        $normalizer = $this->createMock(Normalizer\NormalizerInterface::class);
        $normalizer2 = $this->createMock(Normalizer\DenormalizerInterface::class);

        $registry->register($normalizer, 2);
        $this->assertCount(17, $registry->all());
        $this->assertTrue($registry->has($normalizer::class));

        $registry->register($normalizer2, 1);
        $this->assertCount(18, $registry->all());
        $this->assertTrue($registry->has($normalizer2::class));

        $this->assertSame($normalizer2, $registry->all()[0]);
        $this->assertSame($normalizer, $registry->all()[1]);
    }

    public function testAll(): void
    {
        $unwrapping = new Normalizer\UnwrappingDenormalizer();
        $object = new Normalizer\ObjectNormalizer();

        $registry = new NormalizersRegistry(
            $this->createMock(LoaderInterface::class),
            DebugMode::Enabled,
            [$unwrapping, $object]
        );

        $this->assertSame([$unwrapping, $object], $registry->all());
    }

    public function testHas(): void
    {
        $normalizer = $this->createMock(Normalizer\NormalizerInterface::class);

        $registry = new NormalizersRegistry(
            $this->createMock(LoaderInterface::class),
            DebugMode::Enabled
        );
        $this->assertFalse($registry->has($normalizer::class));

        $registry->register($normalizer);
        $this->assertTrue($registry->has($normalizer::class));
    }
}
