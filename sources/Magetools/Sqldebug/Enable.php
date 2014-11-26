<?php

class Magetools_Sqldebug_Enable extends Magetools_Sqldebug_Abstract
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
    mage.phar --esd|--ensqldebug [-t|--disable-trace|-q|--disable-query]
    mage.phar --esd|--ensqldebug -h | --help

Options:
    -h --help           Show this screen
    -t --disable-trace  Do not enable trace of call stack (by default it is enabled)
    -q --disable-query  Do not enable log of all queries (by default it is enabled)

USAGE;
    }
}
