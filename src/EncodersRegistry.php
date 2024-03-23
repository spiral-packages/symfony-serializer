<?php

declare(strict_types=1);

namespace Spiral\Serializer\Symfony;

use Spiral\Serializer\Symfony\Encoder\ProtobufEncoder;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Encoder;
use Symfony\Component\Yaml\Dumper;
use Google\Protobuf\Internal\Message;

class EncodersRegistry implements EncodersRegistryInterface
{
    /** @var array<class-string, EncoderInterface>  */
    private array $encoders = [];

    /**
     * @param EncoderInterface[] $encoders
     */
    public function __construct(array $encoders = [])
    {
        if ($encoders === []) {
            $this->registerDefaultEncoders();
        } else {
            foreach ($encoders as $encoder) {
                $this->register($encoder);
            }
        }
    }

    public function register(EncoderInterface $encoder): void
    {
        if (!$this->has($encoder::class)) {
            $this->encoders[$encoder::class] = $encoder;
        }
    }

    /**
     * @return EncoderInterface[]
     */
    public function all(): array
    {
        return \array_values($this->encoders);
    }

    /**
     * @psalm-param class-string<EncoderInterface> $className
     */
    public function has(string $className): bool
    {
        return isset($this->encoders[$className]);
    }

    private function registerDefaultEncoders(): void
    {
        $this->register(new Encoder\JsonEncoder());
        $this->register(new Encoder\CsvEncoder());
        $this->register(new Encoder\XmlEncoder());
        if (\class_exists(Dumper::class)) {
            $this->register(new Encoder\YamlEncoder());
        }
        if (\class_exists(Message::class)) {
            $this->register(new ProtobufEncoder());
        }
    }
}
