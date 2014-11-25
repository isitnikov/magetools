#!/usr/bin/env php
<?php
require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'abstract' . DIRECTORY_SEPARATOR . 'sqldebug.abstract.php';

class Magetools_DisableSqlDebug extends Magetools_SqlDebug_Abstract
{
    protected $_scriptName = 'magedissqldebug.php';

    protected function _changeFileContents(&$contents)
    {
        $config = array(
            '$_debug'         => 'false',
            '$_logCallStack'  => 'false',
            '$_logAllQueries' => 'false'
        );

        $patterns = array(
            '/(protected\s+\$_debug\s+\=\s)(true)(\;)/m',
            '/(protected\s+\$_logCallStack\s+\=\s)([A-z]+)(\;)/m',
            '/(protected\s+\$_logAllQueries\s+\=\s)([A-z]+)(\;)/m'
        );
        $replace = array(
            '\\1false\\3',
            '\\1false\\3',
            '\\1false\\3'
        );

        $contents = preg_replace($patterns, $replace, $contents);

        return $config;
    }

    protected function _usageHelp()
    {
        return <<<USAGE
Magetools: Disable SQL debugger

Usage:
    magedissqldebug.php
    magedissqldebug.php -h | --help

    php -f magedissqldebug.php
    php -f magedissqldebug.php -h | --help

    mage.php --dsd|--dissqldebug
    mage.php --dsd|--dissqldebug -h | --help

Options:
    -h --help   Show this screen

USAGE;
    }
}

if (!defined('DO_NOT_RUN')) {
    $run = new Magetools_DisableSqlDebug();
    $run->run();
    exit(0);
}
