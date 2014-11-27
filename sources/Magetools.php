<?php
define('DS', DIRECTORY_SEPARATOR);

spl_autoload_register('Magetools_Autoloader');

final class Magetools
{
    protected static $_routes = array();

    protected static function _getRoutes()
    {
        if (!count(self::$_routes))
        {
            /**
             * @TODO refactor
             */
            self::$_routes = array(
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
                array(
                    'aliases' => array('--c', '--cache'),
                    'class' => 'Cache',
                    'description' => 'Show cache information'
                ),
                array(
                    'aliases' => array('--cc', '--cache-clean'),
                    'class' => 'Cache_Clean',
                    'description' => 'Clean cache'
                ),
                array(
                    'aliases' => array('--ce', '--cache-enable'),
                    'class' => 'Cache',
                    'description' => 'Enable cache'
                ),
                array(
                    'aliases' => array('--cd', '--cache-disable'),
                    'class' => 'Cache',
                    'description' => 'Disable cache'
                ),
            );
        }

        return self::$_routes;
    }

    public static function init()
    {
        if (isset($_SERVER['argv']) && isset($_SERVER['argv'][1])) {
            foreach (self::_getRoutes() as $route) {
                if (in_array($_SERVER['argv'][1], $route['aliases'])) {
                    $className = 'Magetools_' . $route['class'];

                    /** @var Magetools_Abstract $run */
                    $run = new $className();
                    $run->run();
                    exit(0);
                }
            }
        }
        self::_usageHelp();
    }

    private static function _usageHelp()
    {
        $usage = <<<USAGE
Usage:
    mage.phar
    mage.phar -h | --help
    mage.phar <command> [<args>]

Options:
    -h --help           Show this screen

The most commonly used mage commands are:

USAGE;

        foreach (self::_getRoutes() as $route) {
            $usage .= sprintf(
                '    %-21s  %s' . PHP_EOL,
                implode(' | ', $route['aliases']),
                $route['description']
            );
        }

        die($usage . PHP_EOL);
    }
}