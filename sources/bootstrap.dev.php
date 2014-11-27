#!/usr/bin/env php
<?php
function Magetools_Autoloader($className) {
    $classPath = __DIR__ . DS . str_replace('_', '/', $className) . '.php';
    if (!file_exists($classPath)) {
        throw new Exception(
            sprintf(
                'Class "%s" cannot be loaded, because class filename "%s" is not exists',
                $className, $classPath
            )
        );
    }

    require_once $classPath;
}

require_once __DIR__ . '/Magetools.php';

Magetools::init();
