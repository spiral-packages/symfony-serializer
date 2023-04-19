# Symfony serializer bridge for Spiral Framework

[![PHP Version Require](https://poser.pugx.org/spiral-packages/symfony-serializer/require/php)](https://packagist.org/packages/spiral-packages/symfony-serializer)
[![Latest Stable Version](https://poser.pugx.org/spiral-packages/symfony-serializer/v/stable)](https://packagist.org/packages/spiral-packages/symfony-serializer)
[![phpunit](https://github.com/spiral-packages/symfony-serializer/actions/workflows/phpunit.yml/badge.svg)](https://github.com/spiral-packages/symfony-serializer/actions)
[![psalm](https://github.com/spiral-packages/symfony-serializer/actions/workflows/psalm.yml/badge.svg)](https://github.com/spiral-packages/symfony-serializer/actions)
[![Codecov](https://codecov.io/gh/spiral-packages/symfony-serializer/branch/master/graph/badge.svg)](https://codecov.io/gh/spiral-packages/symfony-serializer)
[![Total Downloads](https://poser.pugx.org/spiral-packages/symfony-serializer/downloads)](https://packagist.org/packages/spiral-packages/symfony-serializer)

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

The package is already configured by default, use these features only if you need to change the default configuration.

The package provides the ability to configure the `normalizers`, `encoders` and `Symfony\Component\Serializer\Mapping\Loader\LoaderInterface`
for `Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory` used by the Symfony Serializer component.

Create a file `app/config/symfony-serializer.php`.
Add the `normalizers`, `encoders`, `metadataLoader` parameters. For example:

```php
<?php

declare(strict_types=1);

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

### EncodersRegistry and NormalizersRegistry

The package provides `Spiral\Serializer\Symfony\EncodersRegistryInterface` and
`Spiral\Serializer\Symfony\NormalizersRegistryInterface`. They contain **encoders** and **normalizers/denormalizers**.
You can add your own **encoder** or **normalizer/denormalizer** to them by using the `register` method:

```php
public function boot(
    NormalizersRegistryInterface $normalizersRegistry,
    EncodersRegistryInterface $encodersRegistry
): void {
    // Add CustomNormalizer before ObjectNormalizer
    $normalizersRegistry->register(normalizer: new CustomNormalizer(), priority: 699);

    $encodersRegistry->register(new CustomEncoder());
}
```

## Usage

Using with `Spiral\Serializer\SerializerManager`. For example:
```php
use Spiral\Serializer\SerializerManager;

$serializer = $this->container->get(SerializerManager::class);

$result = $manager->serialize($payload, 'symfony-json');
$result = $manager->serialize($payload, 'symfony-csv');
$result = $manager->serialize($payload, 'symfony-xm');
$result = $manager->serialize($payload, 'symfony-yaml');

$result = $manager->unserialize($payload, Post::class, 'symfony-json');
$result = $manager->unserialize($payload, Post::class, 'symfony-csv');
$result = $manager->unserialize($payload, Post::class, 'symfony-xm');
$result = $manager->unserialize($payload, Post::class, 'symfony-yaml');

// Getting a serializer `Spiral\Serializer\Symfony\Serializer`
$serializer = $manager->getSerializer('symfony-json');

// $serializer->serialize($payload, $context);
// $serializer->unserialize($payload, $type, $context);
// $serializer->normalize($data, $format, $context);
// $serializer->denormalize($data, $type, $format, $context);
// $serializer->supportsNormalization($data, $format, $context);
// $serializer->supportsDenormalization($data, $type, $format, $context);
// $serializer->encode($data, $format, $context);
// $serializer->decode($data, $format, $context);
// $serializer->supportsEncoding($format, $context);
// $serializer->supportsDecoding($format, $context);
```
Using with `Symfony\Component\Serializer\SerializerInterface`. For example:
```php
use Symfony\Component\Serializer\SerializerInterface;

$serializer = $this->container->get(SerializerInterface::class);

$result = $serializer->serialize($payload, 'symfony-json', $context);
$result = $serializer->serialize($payload, 'symfony-csv', $context);
$result = $serializer->serialize($payload, 'symfony-xm', $context);
$result = $serializer->serialize($payload, 'symfony-yaml', $context);

$result = $serializer->deserialize($payload, Post::class, 'symfony-json', $context);
$result = $serializer->deserialize($payload, Post::class, 'symfony-csv', $context);
$result = $serializer->deserialize($payload, Post::class, 'symfony-xm', $context);
$result = $serializer->deserialize($payload, Post::class, 'symfony-yaml', $context);
```

> **Note**
> The `yaml` encoder requires the `symfony/yaml` package and is disabled when the package is not installed.
> Install the `symfony/yaml` package and the encoder will be automatically enabled.

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
