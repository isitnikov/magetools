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
}

if (!defined('DO_NOT_RUN')) {
    $run = new Magetools_EnableSqlDebug();
    $run->run();
    exit(0);
}
