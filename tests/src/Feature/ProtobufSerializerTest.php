<?php

declare(strict_types=1);

namespace Spiral\Serializer\Symfony\Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Service\Message;
use Spiral\Serializer\SerializerManager;
use Symfony\Component\Serializer\SerializerInterface;

final class ProtobufSerializerTest extends TestCase
{
    private SerializerInterface $serializer;
    private SerializerManager $manager;

    public static function formatDataProvider(): iterable
    {
        yield ['proto'];
        yield ['protobuf'];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->serializer = $this->getContainer()->get(SerializerInterface::class);
        $this->manager = $this->getContainer()->get(SerializerManager::class);
    }

    #[DataProvider('formatDataProvider')]
    public function testSerialize(string $format): void
    {
        $message = new Message([
            'msg' => 'Hello, World!',
        ]);

        $data = $this->serializer->serialize($message, $format);

        $this->assertSame($message->serializeToString(), $data);
    }

    #[DataProvider('formatDataProvider')]
    public function testDeserialize(string $format): void
    {
        $message = new Message([
            'msg' => 'Hello, World!',
        ]);


        $this->assertEquals(
            $message,
            $this->serializer->deserialize($message->serializeToString(), Message::class, $format),
        );
    }

    public function testSerializeByManager(): void
    {
        $message = new Message([
            'msg' => 'Hello, World!',
        ]);

        $data = $this->manager->serialize($message, 'symfony-proto');

        $this->assertSame($message->serializeToString(), $data);
    }

    public function testDeserializeByManager(): void
    {
        $message = new Message([
            'msg' => 'Hello, World!',
        ]);

        $data = $this->manager->unserialize($message->serializeToString(), Message::class, 'symfony-proto');

        $this->assertEquals($message, $data);
    }
}
