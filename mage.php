#!/usr/bin/env php
<?php
define('DS', DIRECTORY_SEPARATOR);

$routes = getRoutes();

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

spl_autoload_register('Magetools_Autoloader');


if (isset($argv) && isset($argv[1])) {
    foreach ($routes as $route) {
        if (in_array($argv[1], $route['aliases'])) {
            $className = 'Magetools_' . $route['class'];

            /** @var Magetools_Abstract $run */
            $run = new $className();
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
            'class' => 'Version',
            'description' => 'Show Magento version'
        ),
        array(
            'aliases' => array('--mt', '--modtree'),
            'class' => 'Module_Tree',
            'description' => 'Show module(-s) dependencies tree'
        ),
        array(
            'aliases' => array('--em', '--enmod'),
            'class' => 'Module_Enable',
            'description' => 'Enable specified module'
        ),
        array(
            'aliases' => array('--dm', '--dismod'),
            'class' => 'Module_Disable',
            'description' => 'Disable specified module'
        ),
        array(
            'aliases' => array('--edm', '--endevmode'),
            'class' => 'Indexphp_Devmode_Enable',
            'description' => 'Enable MAGE_IS_DEVELOPER_MODE'
        ),
        array(
            'aliases' => array('--ddm', '--disdevmode'),
            'class' => 'Indexphp_Devmode_Disable',
            'description' => 'Disable MAGE_IS_DEVELOPER_MODE'
        ),
        array(
            'aliases' => array('--ep', '--enprof'),
            'class' => 'Indexphp_Profiler_Enable',
            'description' => 'Enable Varien_Profiler'
        ),
        array(
            'aliases' => array('--dp', '--disprof'),
            'class' => 'Indexphp_Profiler_Disable',
            'description' => 'Disable Varien_Profiler'
        ),
        array(
            'aliases' => array('--esd', '--ensqldebug'),
            'class' => 'Sqldebug_Enable',
            'description' => 'Enable SQL debug'
        ),
        array(
            'aliases' => array('--dsd', '--dissqldebug'),
            'class' => 'Sqldebug_Disable',
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