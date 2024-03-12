<?php

declare(strict_types=1);

namespace Spiral\Serializer\Symfony\Tests\Feature\Normalizer;

use PHPUnit\Framework\Attributes\DataProvider;
use Ramsey\Uuid\Uuid;
use Spiral\Serializer\SerializerManager;
use Spiral\Serializer\Symfony\Tests\App\Object\Author;
use Spiral\Serializer\Symfony\Tests\Feature\TestCase;

final class RamseyUuidNormalizerTest extends TestCase
{
    #[DataProvider('serializeDataProvider')]
    public function testSerialize(string $expected, mixed $payload, string $format): void
    {
        $manager = $this->getContainer()->get(SerializerManager::class);

        $this->assertSame($expected, preg_replace('/\s+/', '', $manager->serialize($payload, $format)));
    }

    public function testUnserialize(): void
    {
        $manager = $this->getContainer()->get(SerializerManager::class);

        $result = $manager->unserialize(
            '{"uuid":"1d96a152-9838-43a0-a189-159befc9e38f","name":"some"}',
            Author::class,
            'symfony-json'
        );

        $this->assertInstanceOf(Author::class, $result);
        $this->assertSame('1d96a152-9838-43a0-a189-159befc9e38f', $result->uuid->toString());
    }

    public static function serializeDataProvider(): \Traversable
    {
        yield [
            '{"uuid":"1d96a152-9838-43a0-a189-159befc9e38f","name":"some"}',
            new Author(Uuid::fromString('1d96a152-9838-43a0-a189-159befc9e38f'), 'some'),
            'symfony-json'
        ];
        yield [
            'uuid,name1d96a152-9838-43a0-a189-159befc9e38f,some',
            new Author(Uuid::fromString('1d96a152-9838-43a0-a189-159befc9e38f'), 'some'),
            'symfony-csv'
        ];
        yield [
            '{uuid:1d96a152-9838-43a0-a189-159befc9e38f,name:some}',
            new Author(Uuid::fromString('1d96a152-9838-43a0-a189-159befc9e38f'), 'some'),
            'symfony-yaml'
        ];
    }
}
