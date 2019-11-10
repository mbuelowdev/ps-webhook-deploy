<?php
/**
 * @author 42Pollux
 * @since 2019-11-03
 */

namespace App\Serializer;


use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Singleton
 * Class Serializer
 * @package App\Serializer
 */
class Serializer implements SerializerInterface
{
    /**
     * @var SerializerInterface
     */
    private static $serializer = null;

    /**
     * @return SerializerInterface
     */
    public static function getInstance()
    {
        if (self::$serializer !== null) {
            return self::$serializer;
        } else {
            $encoders = [new XmlEncoder(), new JsonEncoder()];
            $normalizers = [new ObjectNormalizer()];
            self::$serializer = new \Symfony\Component\Serializer\Serializer($normalizers, $encoders);

            return self::$serializer;
        }
    }

    /**
     * Serializes data in the appropriate format.
     *
     * @param mixed $data Any data
     * @param string $format Format name
     * @param array $context Options normalizers/encoders have access to
     *
     * @return string
     * @throws \Exception
     */
    public function serialize($data, $format = '', array $context = [])
    {
        $serializer = self::getInstance();

        try {
            return $serializer->serialize(
                $data,
                'json',
                $context
            );
        } catch (\Throwable $t) {
            throw new \Exception($t->getMessage(), $t->getCode(), $t);
        }
    }

    /**
     * Deserializes data into the given type.
     *
     * @param mixed $data
     * @param string $type
     * @param string $format
     * @param array $context
     *
     * @return object
     * @throws \Exception
     */
    public function deserialize($data, $type, $format = '', array $context = [])
    {
        $serializer = self::getInstance();

        try {
            return $serializer->deserialize(
                $data,
                $type,
                'json',
                $context
            );
        } catch (\Throwable $t) {
            throw new \Exception($t->getMessage(), $t->getCode(), $t);
        }
    }
}
