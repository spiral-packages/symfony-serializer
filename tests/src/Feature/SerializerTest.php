<?php

declare(strict_types=1);

namespace Spiral\Serializer\Symfony\Tests\Feature;

use Spiral\Serializer\SerializerManager;
use Spiral\Serializer\Symfony\Serializer;
use Spiral\Serializer\Symfony\Tests\App\Object\Post;
use Spiral\Serializer\Symfony\Tests\App\Object\Product;

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

    public function testUnserializeWithAttributes(): void
    {
        $manager = $this->getContainer()->get(SerializerManager::class);

        $result = $manager->unserialize(
            '{"id":1,"title":"Some product","price":100,"active":false,"product_views":5}',
            Product::class,
            'json'
        );
        $this->assertInstanceOf(Product::class, $result);
        $this->assertSame(1, $result->id);
        $this->assertSame('Some product', $result->title);
        $this->assertSame(100.0, $result->price);
        $this->assertFalse($result->active);
        $this->assertSame(5, $result->views);
    }

    public function testGroupNormalize(): void
    {
        $manager = $this->getContainer()->get(SerializerManager::class);
        /** @var Serializer $serializer */
        $serializer = $manager->getSerializer('json');

        $product = new Product(1, 'Some product', 100, false, 5);

        $this->assertSame(
            ['id' => 1, 'title' => 'Some product'],
            $serializer->normalize($product, null, ['groups' => 'group1'])
        );
        $this->assertSame(
            ['price' => 100.0, 'active' => false],
            $serializer->normalize($product, null, ['groups' => 'group2'])
        );
        $this->assertSame(['product_views' => 5], $serializer->normalize($product, null, ['groups' => 'group3']));
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
        yield [
            '{"id":1,"title":"Someproduct","price":100,"active":false,"product_views":5}',
            new Product(1, 'Some product', 100, false, 5),
            'json'
        ];
    }
}
