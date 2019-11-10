<?php
/**
 * @author 42Pollux
 * @since 2019-11-01
 */

namespace App\Helper;


use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class Configuration
{
    /**
     * @var ParameterBagInterface
     */
    private static $parameterBag;

    /**
     * @param ParameterBagInterface $parameterBag
     */
    public static function init(ParameterBagInterface $parameterBag)
    {
        self::$parameterBag = $parameterBag;
    }

    /**
     * @param string $strName
     * @return mixed
     */
    public static function get(string $strName)
    {
        return self::$parameterBag->get($strName);
    }
}