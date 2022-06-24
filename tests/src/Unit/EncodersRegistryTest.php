<?php

declare(strict_types=1);

namespace Spiral\Serializer\Symfony\Tests\Unit;

use Spiral\Serializer\Symfony\EncodersRegistry;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

final class EncodersRegistryTest extends TestCase
{
    public function testConstruct(): void
    {
        $this->assertCount(0, (new EncodersRegistry())->all());

        $registry = new EncodersRegistry([new JsonEncoder(), new CsvEncoder()]);
        $this->assertCount(2, $registry->all());
        $this->assertTrue($registry->has(JsonEncoder::class));
        $this->assertTrue($registry->has(CsvEncoder::class));
    }

    public function testRegister(): void
    {
        $registry = new EncodersRegistry();

        $registry->register(new JsonEncoder());
        $this->assertCount(1, $registry->all());
        $this->assertTrue($registry->has(JsonEncoder::class));

        $registry->register(new JsonEncoder());
        $this->assertCount(1, $registry->all());

        $registry->register(new CsvEncoder());
        $this->assertCount(2, $registry->all());
        $this->assertTrue($registry->has(CsvEncoder::class));
    }

    public function testAll(): void
    {
        $json = new JsonEncoder();
        $csv = new CsvEncoder();

        $this->assertSame([], (new EncodersRegistry())->all());

        $registry = new EncodersRegistry([$json, $csv]);
        $this->assertSame([$json, $csv], $registry->all());
    }

    public function testHas(): void
    {
        $registry = new EncodersRegistry();
        $this->assertFalse($registry->has(JsonEncoder::class));

        $registry->register(new JsonEncoder());
        $this->assertTrue($registry->has(JsonEncoder::class));
    }
}
