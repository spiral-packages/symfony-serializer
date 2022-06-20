<?php

declare(strict_types=1);

namespace Spiral\Serializer\Symfony\Tests\Feature;

use Spiral\Serializer\SerializerManager;
use Spiral\Serializer\Symfony\Tests\App\Object\Post;

final class SerializerTest extends TestCase
{
    /** @dataProvider serializeDataProvider  */
    public function testSerialize(string $expected, mixed $payload, string $format): void
    {
        $manager = $this->getContainer()->get(SerializerManager::class);

        $this->assertSame($expected, preg_replace('/\s+/', '', $manager->serialize($payload, $format)));
    }

    public function testUnserialize(): void
    {
        $manager = $this->getContainer()->get(SerializerManager::class);

        $result = $manager->unserialize('{"id":1,"text":"some","active":false,"views":3}', Post::class, 'json');
        $this->assertInstanceOf(Post::class, $result);
        $this->assertSame(1, $result->id);
        $this->assertSame('some', $result->text);
        $this->assertFalse($result->active);

        $result = $manager->unserialize(
            '{"id":1,"text":"some","active":false,"views":3}',
            new Post(2, '', true, 1),
            'json'
        );
        $this->assertInstanceOf(Post::class, $result);
        $this->assertSame(1, $result->id);
        $this->assertSame('some', $result->text);
        $this->assertFalse($result->active);
    }

    public function serializeDataProvider(): \Traversable
    {
        yield ['{"id":1,"text":"some","active":false,"views":3}', new Post(1, 'some', false, 3), 'json'];
        yield ['id,text,active,views1,some,1,5', new Post(1, 'some', true, 5), 'csv'];
        yield ['{id:1,text:some,active:true,views:5}', new Post(1, 'some', true, 5), 'yaml'];
        yield [
            '<?xmlversion="1.0"?><response><id>1</id><text>some</text><active>1</active><views>5</views></response>',
            new Post(1, 'some', true, 5),
            'xml'
        ];
    }
}
