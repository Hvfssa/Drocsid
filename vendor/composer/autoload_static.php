<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite36185cd65a51f14c994a1be8da3b013
{
    public static $files = array (
        'da253f61703e9c22a5a34f228526f05a' => __DIR__ . '/..' . '/wixel/gump/gump.class.php',
    );

    public static $prefixLengthsPsr4 = array (
        'G' => 
        array (
            'GUMP\\' => 5,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'GUMP\\' => 
        array (
            0 => __DIR__ . '/..' . '/wixel/gump/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInite36185cd65a51f14c994a1be8da3b013::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite36185cd65a51f14c994a1be8da3b013::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInite36185cd65a51f14c994a1be8da3b013::$classMap;

        }, null, ClassLoader::class);
    }
}