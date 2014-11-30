<?php

class Magetools_Indexphp_Devmode_Enable extends Magetools_Indexphp_Abstract
{
    protected function _changeFileContents(&$contents)
    {
        $patterns = array(
            '/(if\s+\(isset\(\$_SERVER\[\'MAGE_IS_DEVELOPER_MODE\']\).+)(\R+)(.*)(\R+)(\}.*)/m',
            '/([#|\/]+\s*)(ini_set\(\'display_errors\'.*)/m'
        );
        $replace = array(
            '//\\1\\2\\3\\4//\\5',
            '\\2'
        );

        $contents = preg_replace($patterns, $replace, $contents);

        $this->_printMessage('MAGE_IS_DEVELOPER_MODE is enabled');
        $this->_printMessage("ini_set('display_errors'); is uncommented");
    }

    protected function _usageHelp()
    {
        return <<<USAGE
Magetools: Enable MAGE_IS_DEVELOPER_MODE

Usage:
    mage.phar --edm|--endevmode
    mage.phar --edm|--endevmode -h | --help

Options:
    -h --help   Show this screen

USAGE;
    }
}
