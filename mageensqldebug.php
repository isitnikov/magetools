#!/usr/bin/env php
<?php
require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'abstract' . DIRECTORY_SEPARATOR . 'sqldebug.abstract.php';

class Magetools_EnableSqlDebug extends Magetools_SqlDebug_Abstract
{
    protected $_opts = 'htq';

    protected $_longOpts = array(
        'help', 'disable-trace', 'disable-query'
    );

    protected $_scriptName = 'mageensqldebug.php';

    protected $_optsMap = array(
        'help' => 'h',
        'disable-trace' => 't',
        'disable-query' => 'q'
    );

    protected function _changeFileContents(&$contents)
    {
        $config   = array('$_debug' => 'true');
        $patterns = array('/(protected\s+\$_debug\s+\=\s)([A-z]+)(\;)/m');
        $replace  = array('\\1true\\3');

        if (!$this->_getOpt('disable-trace')) {
            $config['$_logCallStack'] = 'true';
            $patterns[] = '/(protected\s+\$_logCallStack\s+\=\s)([A-z]+)(\;)/m';
            $replace[] = '\\1true\\3';
        } else {
            $config['$_logCallStack'] = 'false';
            $patterns[] = '/(protected\s+\$_logCallStack\s+\=\s)([A-z]+)(\;)/m';
            $replace[] = '\\1false\\3';
        }

        if (!$this->_getOpt('disable-query')) {
            $config['$_logAllQueries'] = 'true';
            $patterns[] = '/(protected\s+\$_logAllQueries\s+\=\s)([A-z]+)(\;)/m';
            $replace[] = '\\1true\\3';
        } else {
            $config['$_logAllQueries'] = 'false';
            $patterns[] = '/(protected\s+\$_logAllQueries\s+\=\s)([A-z]+)(\;)/m';
            $replace[] = '\\1false\\3';
        }

        $contents = preg_replace($patterns, $replace, $contents);

        return $config;
    }

    protected function _usageHelp()
    {
        return <<<USAGE
Magetools: Enable SQL debugger

Usage:
    mageensqldebug.php [-t|--disable-trace|-q|--disable-query]
    mageensqldebug.php -h | --help

    php -f mageensqldebug.php [-t|--disable-trace|-q|--disable-query]
    php -f mageensqldebug.php -h | --help

    mage.php --esd|--ensqldebug [-t|--disable-trace|-q|--disable-query]
    mage.php --esd|--ensqldebug -h | --help

Options:
    -h --help           Show this screen
    -t --disable-trace  Do not enable trace of call stack (by default it is enabled)
    -q --disable-query  Do not enable log of all queries (by default it is enabled)

USAGE;
    }
}

if (!defined('DO_NOT_RUN')) {
    $run = new Magetools_EnableSqlDebug();
    $run->run();
    exit(0);
}
