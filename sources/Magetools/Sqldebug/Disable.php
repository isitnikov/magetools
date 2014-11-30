<?php

class Magetools_Sqldebug_Disable extends Magetools_Sqldebug_Abstract
{
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
    mage.phar --dsd|--dissqldebug
    mage.phar --dsd|--dissqldebug -h | --help

Options:
    -h --help   Show this screen

USAGE;
    }
}
