<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit874118fe80144418c94db0b2c773e37f
{
    public static $prefixLengthsPsr4 = array (
        'a' => 
        array (
            'andreskrey\\Readability\\' => 23,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'andreskrey\\Readability\\' => 
        array (
            0 => __DIR__ . '/..' . '/andreskrey/readability.php/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit874118fe80144418c94db0b2c773e37f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit874118fe80144418c94db0b2c773e37f::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
