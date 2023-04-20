# Symfony serializer bridge for Spiral Framework

[![PHP Version Require](https://poser.pugx.org/spiral-packages/symfony-serializer/require/php)](https://packagist.org/packages/spiral-packages/symfony-serializer)
[![Latest Stable Version](https://poser.pugx.org/spiral-packages/symfony-serializer/v/stable)](https://packagist.org/packages/spiral-packages/symfony-serializer)
[![phpunit](https://github.com/spiral-packages/symfony-serializer/actions/workflows/phpunit.yml/badge.svg)](https://github.com/spiral-packages/symfony-serializer/actions)
[![psalm](https://github.com/spiral-packages/symfony-serializer/actions/workflows/psalm.yml/badge.svg)](https://github.com/spiral-packages/symfony-serializer/actions)
[![Total Downloads](https://poser.pugx.org/spiral-packages/symfony-serializer/downloads)](https://packagist.org/packages/spiral-packages/symfony-serializer)

This package provides an extension to the default list of serializers in Spiral Framework, allowing you to easily
serialize and deserialize objects into various formats such as `JSON`, `XML`, `CSV`, and `YAML`.

> **Note**
> Read more about spiral/serializer component in the
> official [documentation](https://spiral.dev/docs/advanced-serializer).

If you are building a REST API or working with queues, this package will be especially useful as it allows you to use
objects as payload instead of simple arrays.

This documentation will guide you through the installation process and provide examples of how to use the package to
serialize and deserialize your objects.

## Requirements

Make sure that your server is configured with following PHP version and extensions:

- PHP 8.1+
- Spiral framework ^3.7
- Symfony Serializer Component ^5.4 || ^6.0
- Symfony PropertyAccess Component ^5.4 || ^6.0

## Installation

You can install the package via composer:

```bash
composer require spiral-packages/symfony-serializer
```

After package install you need to register bootloader from the package.

```php
protected const LOAD = [
    // ...
    \Spiral\Serializer\Symfony\Bootloader\SerializerBootloader::class,
];
```

> **Note**
> Bootloader `Spiral\Serializer\Bootloader\SerializerBootloader` can be removed.
> If you are using [`spiral-packages/discoverer`](https://github.com/spiral-packages/discoverer),
> you don't need to register bootloader by yourself.

## Configuration

The package comes with default configurations for `normalizers`, `encoders`, and `metadataLoader`. However, you can
change these configurations based on your project's requirements.

**There are two ways to configure the package:**

### Config file

You can create a configuration file `app/config/symfony-serializer.php` and define `normalizers`, `encoders`,
and `Symfony\Component\Serializer\Mapping\Loader\LoaderInterface`
for `Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory` used by the Symfony Serializer component
parameters to extend the default configuration.

**Here is an example of the configuration file:**

```php
use Symfony\Component\Serializer\Encoder;
use Symfony\Component\Serializer\Normalizer;
use Spiral\Core\Container\Autowire;

return [
    'normalizers' => [
        new Normalizer\UnwrappingDenormalizer(),
        new Normalizer\ProblemNormalizer(debug: false),
        new Normalizer\UidNormalizer(),
        new Normalizer\JsonSerializableNormalizer(),
        new Normalizer\DateTimeNormalizer(),
        new Normalizer\ConstraintViolationListNormalizer(),
        new Normalizer\MimeMessageNormalizer(new Normalizer\PropertyNormalizer()),
        new Normalizer\DateTimeZoneNormalizer(),
        new Normalizer\DateIntervalNormalizer(),
        new Normalizer\FormErrorNormalizer(),
        new Normalizer\BackedEnumNormalizer(),
        new Normalizer\DataUriNormalizer(),
        new Autowire(Normalizer\ArrayDenormalizer::class), // by Autowire
        Normalizer\ObjectNormalizer::class, // by class string
    ],
    'encoders' => [
        new Encoder\JsonEncoder(),
        new Encoder\CsvEncoder(),
        Encoder\XmlEncoder::class,
        new Autowire(Encoder\YamlEncoder::class),
    ],
    'metadataLoader' => new AnnotationLoader(new AnnotationReader()) // by default
 //  Other available loaders:
 // 'metadataLoader' => new YamlFileLoader('/path/to/your/definition.yaml')
 // 'metadataLoader' => new XmlFileLoader('/path/to/your/definition.xml')
];
```

### Bootloader

`Spiral\Serializer\Symfony\EncodersRegistryInterface` and `Spiral\Serializer\Symfony\NormalizersRegistryInterface`
provided by the package to add your own normalizers or encoders. You can register your own `normalizers` or `encoders`
using the `register` method provided by these interfaces.

**Here is an example:**

```php
namespace App\Application\Bootloader;

use Spiral\Serializer\Symfony\EncodersRegistryInterface;
use Spiral\Serializer\Symfony\NormalizersRegistryInterface;
use Spiral\Boot\Bootloader\Bootloader;

final class AppBootloader extends Bootloader
{
    public function boot(
        NormalizersRegistryInterface $normalizersRegistry,
        EncodersRegistryInterface $encodersRegistry,
    ): void {
        // Add CustomNormalizer before ObjectNormalizer
        $normalizersRegistry->register(normalizer: new CustomNormalizer(), priority: 699);

        $encodersRegistry->register(new CustomEncoder());
    }
}
```

## Usage

The package provides a list of serializers that can be used to serialize and deserialize objects.

The serializers available in this package are: `symfony-json`, `symfony-csv`, `symfony-xml`, `symfony-yaml`.

> **Warning**
> The `yaml` encoder requires the `symfony/yaml` package and is disabled when the package is not installed.
> Install the `symfony/yaml` package and the encoder will be automatically enabled.

**Here are several ways to use these serializers:**

### 1. Set a default serializer

You can set a desired Symfony serializer as the default application serializer by setting
the `DEFAULT_SERIALIZER_FORMAT` environment variable.

```dotenv
DEFAULT_SERIALIZER_FORMAT=symfony-json
```

Once the default serializer is set, you can request the `Spiral\Serializer\SerializerInterface` from the container and
use it to serialize and deserialize objects.

**Here's an example:**

```php
use Spiral\Serializer\SerializerInterface;
use App\Repository\PostRepository;

finalclass PostController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly PostRepository $repository,
    ) {}

    public function show(string $postId): string
    {
        $post = $this->repository->find($postId);

        return $this->serializer->serialize($post);
    }
}
```

### 2. Using with the Serializer Manager

You can request a desired serializer from `Spiral\Serializer\SerializerManager` by its name. Once you have the
serializer, you can use it to serialize and deserialize objects.

**Here's an example:**

```php
use Spiral\Serializer\SerializerManager;
use Spiral\Serializer\SerializerInterface;
use App\Repository\PostRepository;

final class PostController
{
    private readonly SerializerInterface $serializer,

    public function __construct(
        SerializerManager $manager,
        private readonly PostRepository $repository,
    ) {
        $this->serializer = $manager->getSerializer('symfony-json');
    }

    public function show(string $postId): string
    {
        $post = $this->repository->find($postId);

        return $this->serializer->serialize($post);
    }
}
```

Alternatively, you can use the `serialize` and `unserialize` methods of the manager class:

```php
use Psr\Container\ContainerInterface;
use Spiral\Serializer\SerializerManager;
use App\Repository\PostRepository;
use App\Entity\Post;

/** @var PostRepository $repository */
$post = $repository->find($postId);
/** @var ContainerInterface $container */
$serializer = $container->get(SerializerManager::class);

$serializedString = $manager->serialize($post , 'symfony-json');

$post = $manager->unserialize($serializedString , Post::class, 'symfony-json');
```

### 3. Using with Symfony\Component\Serializer\SerializerInterface

You can also use the Symfony Serializer directly by requesting the `Symfony\Component\Serializer\SerializerInterface`
from the container. Once you have the serializer, you can use it to `serialize` and `deserialize` objects.

**Here's an example:**

```php
use Symfony\Component\Serializer\SerializerInterface;

$serializer = $this->container->get(SerializerInterface::class);

$result = $serializer->serialize($payload, 'symfony-json', $context);
$result = $serializer->deserialize($payload, Post::class, 'symfony-json', $context);
```

### Additional methods

Symfony Serializer Manager provides additional methods to work with data:

- **normalize**: This method takes in `data` and a `format` and returns a value that represents the normalized data. The
  context parameter can also be passed to control the normalization process.

- **denormalize**: This method takes in `data`, a `type`, a `format`, and a `context`, and returns an object that
  represents the denormalized data.

- **supportsNormalization**: This method takes in `data`, a `format`, and a `context`, and returns a `boolean`
  indicating whether the given data can be normalized by the serializer.

- **supportsDenormalization**: This method takes in `data`, a `type`, a `format`, and a `context`, and returns a
  `boolean` indicating whether the given data can be denormalized by the serializer.

- **encode**: This method takes in `data`, a `format`, and a `context`, and returns a `string` that represents the
  encoded data.

- **decode**: This method takes in `data`, a `format`, and a `context`, and returns a `value` that represents the
  decoded data.

- **supportsEncoding**: This method takes in a `format` and a `context`, and returns a `boolean` indicating whether the
  given format can be used to encode data by the serializer.

- **supportsDecoding**: This method takes in a `format` and a `context`, and returns a `boolean` indicating whether the
  given format can be used to decode data by the serializer.

```php
use Spiral\Serializer\SerializerManager;

$manager = $this->container->get(SerializerManager::class);

// Getting a serializer `Spiral\Serializer\Symfony\Serializer`
$serializer = $manager->getSerializer('symfony-json');

$serializer->normalize($data, $format, $context);
$serializer->denormalize($data, $type, $format, $context);

$serializer->supportsNormalization($data, $format, $context);
$serializer->supportsDenormalization($data, $type, $format, $context);

$serializer->encode($data, $format, $context);
$serializer->decode($data, $format, $context);

$serializer->supportsEncoding($format, $context);
$serializer->supportsDecoding($format, $context);
```

These methods provide additional flexibility for working with different data formats and can be useful in certain
scenarios.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
