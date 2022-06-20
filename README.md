# Symfony serializer bridge for Spiral Framework

[![PHP](https://img.shields.io/packagist/php-v/spiral-packages/symfony-serializer.svg?style=flat-square)](https://packagist.org/packages/spiral-packages/symfony-serializer)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/spiral-packages/symfony-serializer.svg?style=flat-square)](https://packagist.org/packages/spiral-packages/symfony-serializer)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/spiral-packages/symfony-serializer/run-tests?label=tests&style=flat-square)](https://github.com/spiral-packages/symfony-serializer/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/spiral-packages/symfony-serializer.svg?style=flat-square)](https://packagist.org/packages/spiral-packages/symfony-serializer)

## Requirements

Make sure that your server is configured with following PHP version and extensions:

- PHP 8.1+
- Spiral framework ^3.0
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

The package provides the ability to configure the `normalizers` and `encoders` used by the Symfony Serializer component.
Create a file `app/config/symfony-serializer.php`.
Add the `normalizers` and `encoders` arrays with the `normalizers` and `encoders` you need. For example:
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
];
```

## Usage
Using with `Spiral\Serializer\SerializerManager`. For example:
```php
use Spiral\Serializer\SerializerManager;

$serializer = $this->container->get(SerializerManager::class); 

$result = $manager->serialize($payload, 'json');
$result = $manager->serialize($payload, 'csv');
$result = $manager->serialize($payload, 'xml');
$result = $manager->serialize($payload, 'yaml');

$result = $manager->unserialize($payload, Post::class, 'json');
$result = $manager->unserialize($payload, Post::class, 'csv');
$result = $manager->unserialize($payload, Post::class, 'xml');
$result = $manager->unserialize($payload, Post::class, 'yaml');

// Getting a serializer `Spiral\Serializer\Symfony\Serializer`
$serializer = $manager->getSerializer('json');

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

$result = $serializer->serialize($payload, 'json', $context);
$result = $serializer->serialize($payload, 'csv', $context);
$result = $serializer->serialize($payload, 'xml', $context);
$result = $serializer->serialize($payload, 'yaml', $context);

$result = $serializer->deserialize($payload, Post::class, 'json', $context);
$result = $serializer->deserialize($payload, Post::class, 'csv', $context);
$result = $serializer->deserialize($payload, Post::class, 'xml', $context);
$result = $serializer->deserialize($payload, Post::class, 'yaml', $context);
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
