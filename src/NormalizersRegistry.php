<?php

declare(strict_types=1);

namespace Spiral\Serializer\Symfony;

use Spiral\Boot\Environment\DebugMode;
use Symfony\Component\Messenger\Transport\Serialization\Normalizer\FlattenExceptionNormalizer;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Mapping\Loader\LoaderInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer;

class NormalizersRegistry implements NormalizersRegistryInterface
{
    /**
     * @var array<class-string, array{priority: int<0, max>, normalizer: NormalizerInterface|DenormalizerInterface}>
     */
    private array $normalizers = [];

    public function __construct(
        protected readonly LoaderInterface $loader,
        protected readonly DebugMode $debugMode,
        array $normalizers = []
    ) {
        if ($normalizers === []) {
            $this->registerDefaultNormalizers();
        } else {
            foreach ($normalizers as $normalizer) {
                $this->register($normalizer);
            }
        }
    }

    /**
     * @param int<0, max> $priority
     */
    public function register(NormalizerInterface|DenormalizerInterface $normalizer, int $priority = 0): void
    {
        if (!$this->has($normalizer::class)) {
            $this->normalizers[$normalizer::class] = [
                'priority' => $priority,
                'normalizer' => $normalizer
            ];
        }
    }

    /**
     * @return array<NormalizerInterface|DenormalizerInterface>
     */
    public function all(): array
    {
        // Sort normalizers by priority
        \uasort(
            $this->normalizers,
            static fn (array $normalizer1, array $normalizer2) => $normalizer1['priority'] <=> $normalizer2['priority']
        );

        return \array_column($this->normalizers, 'normalizer');
    }

    /** @psalm-param class-string $className */
    public function has(string $className): bool
    {
        return isset($this->normalizers[$className]);
    }

    private function registerDefaultNormalizers(): void
    {
        $factory = new ClassMetadataFactory($this->loader);

        $this->register(new Normalizer\UnwrappingDenormalizer(), 50);
        $this->register(new Normalizer\ProblemNormalizer(debug: $this->debugMode->isEnabled()), 100);
        $this->register(new Normalizer\UidNormalizer(), 150);
        $this->register(new Normalizer\JsonSerializableNormalizer(), 200);
        $this->register(new Normalizer\DateTimeNormalizer(), 250);
        $this->register(new Normalizer\ConstraintViolationListNormalizer(), 300);
        $this->register(new Normalizer\MimeMessageNormalizer(new Normalizer\PropertyNormalizer()), 350);
        $this->register(new Normalizer\DateTimeZoneNormalizer(), 400);
        $this->register(new Normalizer\DateIntervalNormalizer(), 450);
        $this->register(new Normalizer\FormErrorNormalizer(), 500);
        $this->register(new Normalizer\BackedEnumNormalizer(), 550);
        $this->register(new Normalizer\DataUriNormalizer(), 600);
        $this->register(new Normalizer\ArrayDenormalizer(), 650);
        $this->register(new Normalizer\ObjectNormalizer(
            classMetadataFactory: $factory,
            nameConverter: new MetadataAwareNameConverter($factory),
            propertyTypeExtractor: new PropertyInfoExtractor(
                typeExtractors: [new PhpDocExtractor(), new ReflectionExtractor()]
            )
        ), 700);

        // Normalizer from symfony/messenger, if exists
        if (\class_exists(FlattenExceptionNormalizer::class)) {
            $this->register(new FlattenExceptionNormalizer(), 750);
        }
    }
}
