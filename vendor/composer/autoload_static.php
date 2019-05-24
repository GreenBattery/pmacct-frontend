<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit4255247f516f5a4c5973d792a62cd835
{
    public static $files = array (
        'f084d01b0a599f67676cffef638aa95b' => __DIR__ . '/..' . '/smarty/smarty/libs/bootstrap.php',
    );

    public static $prefixLengthsPsr4 = array (
        'n' => 
        array (
            'nucc1\\' => 6,
        ),
        'R' => 
        array (
            'RedBeanPHP\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'nucc1\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib',
        ),
        'RedBeanPHP\\' => 
        array (
            0 => __DIR__ . '/..' . '/gabordemooij/redbean/RedBeanPHP',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit4255247f516f5a4c5973d792a62cd835::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit4255247f516f5a4c5973d792a62cd835::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
