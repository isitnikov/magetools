#!/usr/bin/env php
<?php
$routes = getRoutes();

if (isset($argv) && isset($argv[1])) {
    define('DO_NOT_RUN', true);
    $path = dirname(__FILE__);

    foreach ($routes as $route) {
        if (in_array($argv[1], $route['aliases'])) {
            ob_start();
            require_once __DIR__ . DIRECTORY_SEPARATOR . $route['file'];
            ob_end_clean();

            /** @var Magetools_Abstract $run */
            $run = new $route['class']();
            $run->run();
            exit(0);
        }
    }
}

usageHelp();

function getRoutes() {
    return array(
        array(
            'aliases' => array('--v', '--version'),
            'file' => 'mageversion.php',
            'class' => 'Magetools_Version',
            'description' => 'Show Magento version'
        ),
        array(
            'aliases' => array('--mt', '--modtree'),
            'file' => 'magemodtree.php',
            'class' => 'Magetools_ModulesTree',
            'description' => 'Show module(-s) dependencies tree'
        ),
        array(
            'aliases' => array('--em', '--enmod'),
            'file' => 'mageenmod.php',
            'class' => 'Magetools_EnableModule',
            'description' => 'Enable specified module'
        ),
        array(
            'aliases' => array('--dm', '--dismod'),
            'file' => 'magedismod.php',
            'class' => 'Magetools_DisableModule',
            'description' => 'Disable specified module'
        ),
        array(
            'aliases' => array('--edm', '--endevmode'),
            'file' => 'magedisdevmode.php',
            'class' => 'Magetools_EnableDevMode',
            'description' => 'Enable MAGE_IS_DEVELOPER_MODE'
        ),
        array(
            'aliases' => array('--ddm', '--disdevmode'),
            'file' => 'mageendevmode.php',
            'class' => 'Magetools_DisableDevMode',
            'description' => 'Disable MAGE_IS_DEVELOPER_MODE'
        ),
        array(
            'aliases' => array('--ep', '--enprof'),
            'file' => 'magedisprof.php',
            'class' => 'Magetools_EnableProfiler',
            'description' => 'Enable Varien_Profiler'
        ),
        array(
            'aliases' => array('--dp', '--disprof'),
            'file' => 'mageenprof.php',
            'class' => 'Magetools_DisableProfiler',
            'description' => 'Disable Varien_Profiler'
        ),
        array(
            'aliases' => array('--esd', '--ensqldebug'),
            'file' => 'magedissqldebug.php',
            'class' => 'Magetools_EnableSqlDebug',
            'description' => 'Enable SQL debug'
        ),
        array(
            'aliases' => array('--dsd', '--dissqldebug'),
            'file' => 'mageensqldebug.php',
            'class' => 'Magetools_DisableSqlDebug',
            'description' => 'Disable SQL debug'
        ),
    );
}

function usageHelp() {
    $usage = <<<USAGE
Usage:
    mage.php
    mage.php -h | --help
    mage.php <command> [<args>]

Options:
    -h --help           Show this screen

The most commonly used mage commands are:

USAGE;

    foreach (getRoutes() as $route) {
        $usage .= sprintf(
            '    %-21s  %s' . PHP_EOL,
            implode(' | ', $route['aliases']),
            $route['description']
        );
    }

    die($usage . PHP_EOL);
}