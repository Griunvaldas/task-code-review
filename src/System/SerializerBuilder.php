<?php

namespace App\System;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;

class SerializerBuilder
{
    private static ?SerializerInterface $serializer = null;

    public static function build(): SerializerInterface
    {
        if (!self::$serializer instanceof SerializerInterface) {
            $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
            $metadataAwareNameConverter = new MetadataAwareNameConverter($classMetadataFactory);
            $extractor = new PropertyInfoExtractor([], [new PhpDocExtractor(), new ReflectionExtractor()]);
            $normalizers = [
                new PropertyNormalizer(
                    $classMetadataFactory,
                    $metadataAwareNameConverter,
                    $extractor,
                ),
                new ArrayDenormalizer(),
            ];
            $encoders = [
                JsonEncoder::FORMAT => new JsonEncoder(),
            ];

            self::$serializer = new Serializer($normalizers, $encoders);
        }

        return self::$serializer;
    }
}
