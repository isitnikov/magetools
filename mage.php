#!/usr/bin/env php
<?php
$errorMessage = "Cannot find this utility" . PHP_EOL;

if (isset($argv) && isset($argv[1]) && count($argv) > 0) {
    define('DO_NOT_RUN', true);
    $path = dirname(__FILE__);

    $run = null;
    switch($argv[1]) {
        case "--v":
        case "--version":
            ob_start();
            require_once $path . DIRECTORY_SEPARATOR . 'mageversion.php';
            ob_end_clean();

            $run = new Magetools_Version();
            break;

        case "--mt":
        case "--modtree":
            ob_start();
            require_once $path . DIRECTORY_SEPARATOR . 'magemodtree.php';
            ob_end_clean();

            $run = new Magetools_ModulesTree();
            break;

        case "--dm":
        case "--dismod":
            ob_start();
            require_once $path . DIRECTORY_SEPARATOR . 'magedismod.php';
            ob_end_clean();

            $run = new Magetools_DisableModule();
            break;

        case "--em":
        case "--enmod":
            ob_start();
            require_once $path . DIRECTORY_SEPARATOR . 'mageenmod.php';
            ob_end_clean();

            $run = new Magetools_EnableModule();
            break;

        case "--edm":
        case "--endevmod":
            ob_start();
            require_once $path . DIRECTORY_SEPARATOR . 'mageendevmode.php';
            ob_end_clean();

            $run = new Magetools_EnableDevMode();
            break;

        case "--ddm":
        case "--disdevmod":
            ob_start();
            require_once $path . DIRECTORY_SEPARATOR . 'magedisdevmode.php';
            ob_end_clean();

            $run = new Magetools_DisableDevMode();
            break;

        case "--ep":
        case "--enprof":
            ob_start();
            require_once $path . DIRECTORY_SEPARATOR . 'mageenprof.php';
            ob_end_clean();

            $run = new Magetools_EnableProfiler();
            break;

        case "--dp":
        case "--disprof":
            ob_start();
            require_once $path . DIRECTORY_SEPARATOR . 'magedisprof.php';
            ob_end_clean();

            $run = new Magetools_DisableProfiler();
            break;

        case "--esd":
        case "--ensqldebug":
            ob_start();
            require_once $path . DIRECTORY_SEPARATOR . 'mageensqldebug.php';
            ob_end_clean();

            $run = new Magetools_EnableSqlDebug();
            break;

        case "--dsd":
        case "--dissqldebug":
            ob_start();
            require_once $path . DIRECTORY_SEPARATOR . 'magedissqldebug.php';
            ob_end_clean();

            $run = new Magetools_DisableSqlDebug();
            break;

        default:
            die($errorMessage);
            break;
    }

    if ($run) {
        $run->run();
        exit(0);
    } else {
        die($errorMessage);
    }
} else {
    die($errorMessage);
}