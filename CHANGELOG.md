# Changelog

## 2.1.0 - 2023-06-08
- **Other Features**
  - Added the `Spiral\Serializer\Symfony\Normalizer\RamseyUuidNormalizer` class to normalize and denormalize
    UUID objects.

## 2.0.0 - 2023-04-19
- **High Impact Changes**
  - Since version **2.0.0**, serializers are registered with names `symfony-json`, `symfony-csv`,
    `symfony-xml`, `symfony-yaml`. To avoid name conflicts with other serializers.
  - Since version **2.0.0**, methods `getEncoders`, and `getNormalizers`, of the class
    `Spiral\Serializer\Symfony\Config\SerializerConfig` returns empty arrays by default. Default **encoders**
    and **normalizers** are configured in `Spiral\Serializer\Symfony\EncodersRegistry` and
    `Spiral\Serializer\Symfony\NormalizersRegistry`.
  - Removed methods `getDefaultEncoders` and `getDefaultNormalizers` in the configuration class
    `Spiral\Serializer\Symfony\Config\SerializerConfig`.
- **Other Features**
  - Added the **priority** parameter to the `register` method of interface
    `Spiral\Serializer\Symfony\NormalizersRegistryInterface`. This will allow normalizers and denormalizers
    to be registered in the correct order. Default ones are added with a priority that allows you to add your own
    between them.

## 1.1.0 - 2023-03-31
- **Other Features**
  - Allowed `doctrine/annotations:2.0`
  - Psalm v5

## 1.0.0 - 2022-09-14

- initial release
