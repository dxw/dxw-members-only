<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit445d2136251c20212a1e12fd7880f846
{
    public static $files = array (
        'decc78cc4436b1292c6c0d151b19445c' => __DIR__ . '/..' . '/phpseclib/phpseclib/phpseclib/bootstrap.php',
    );

    public static $prefixLengthsPsr4 = array (
        'p' => 
        array (
            'phpseclib\\' => 10,
        ),
        'M' => 
        array (
            'Missing\\' => 8,
        ),
        'D' => 
        array (
            'Dxw\\Result\\' => 11,
            'Dxw\\CIDR\\' => 9,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'phpseclib\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpseclib/phpseclib/phpseclib',
        ),
        'Missing\\' => 
        array (
            0 => __DIR__ . '/..' . '/dxw/php-missing/src',
        ),
        'Dxw\\Result\\' => 
        array (
            0 => __DIR__ . '/..' . '/dxw/result/src',
        ),
        'Dxw\\CIDR\\' => 
        array (
            0 => __DIR__ . '/..' . '/dxw/cidr/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit445d2136251c20212a1e12fd7880f846::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit445d2136251c20212a1e12fd7880f846::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit445d2136251c20212a1e12fd7880f846::$classMap;

        }, null, ClassLoader::class);
    }
}
