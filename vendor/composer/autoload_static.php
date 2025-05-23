<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit134ca55d9bd30e5502ce04bc7d7dce58
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit134ca55d9bd30e5502ce04bc7d7dce58::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit134ca55d9bd30e5502ce04bc7d7dce58::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit134ca55d9bd30e5502ce04bc7d7dce58::$classMap;

        }, null, ClassLoader::class);
    }
}
