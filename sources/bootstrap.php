<?php

function Magetools_Autoloader($className) {
    $classPath = 'phar://mage.phar/' . str_replace('_', '/', $className) . '.php';
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

require_once 'phar://mage.phar/Magetools.php';

Magetools::init();
