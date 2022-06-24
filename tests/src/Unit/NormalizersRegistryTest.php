<?php

declare(strict_types=1);

namespace Spiral\Serializer\Symfony\Tests\Unit;

use Spiral\Serializer\Symfony\NormalizersRegistry;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\UnwrappingDenormalizer;

final class NormalizersRegistryTest extends TestCase
{
    public function testConstruct(): void
    {
        $this->assertCount(0, (new NormalizersRegistry())->all());

        $registry = new NormalizersRegistry([new UnwrappingDenormalizer(), new ObjectNormalizer()]);
        $this->assertCount(2, $registry->all());
        $this->assertTrue($registry->has(UnwrappingDenormalizer::class));
        $this->assertTrue($registry->has(ObjectNormalizer::class));
    }

    public function testRegister(): void
    {
        $registry = new NormalizersRegistry();

        $registry->register(new UnwrappingDenormalizer());
        $this->assertCount(1, $registry->all());
        $this->assertTrue($registry->has(UnwrappingDenormalizer::class));

        $registry->register(new UnwrappingDenormalizer());
        $this->assertCount(1, $registry->all());

        $registry->register(new ObjectNormalizer());
        $this->assertCount(2, $registry->all());
        $this->assertTrue($registry->has(ObjectNormalizer::class));
    }

    public function testAll(): void
    {
        $unwrapping = new UnwrappingDenormalizer();
        $object = new ObjectNormalizer();

        $this->assertSame([], (new NormalizersRegistry())->all());

        $registry = new NormalizersRegistry([$unwrapping, $object]);
        $this->assertSame([$unwrapping, $object], $registry->all());
    }

    public function testHas(): void
    {
        $registry = new NormalizersRegistry();
        $this->assertFalse($registry->has(UnwrappingDenormalizer::class));

        $registry->register(new UnwrappingDenormalizer());
        $this->assertTrue($registry->has(UnwrappingDenormalizer::class));
    }
}
