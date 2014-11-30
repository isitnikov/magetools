<?php

class Magetools_Indexphp_Devmode_Disable extends Magetools_Indexphp_Abstract
{
    protected function _changeFileContents(&$contents)
    {
        $patterns = array(
            '/(.*)(if\s+\(isset\(\$_SERVER\[\'MAGE_IS_DEVELOPER_MODE\'\]\).+)(\R+)(.*)(\R+)(.*)(\}.*)/m',
            '/(.*)(ini_set\(\'display_errors\'.*)/m'
        );
        $replace = array(
            '\\2\\3\\4\\5\\7',
            '#\\2'
        );

        $contents = preg_replace($patterns, $replace, $contents);

        $this->_printMessage('MAGE_IS_DEVELOPER_MODE is disabled');
        $this->_printMessage("ini_set('display_errors'); is commented");
    }

    protected function _usageHelp()
    {
        return <<<USAGE
Magetools: Disable MAGE_IS_DEVELOPER_MODE

Usage:
    mage.phar --ddm|--disdevmode
    mage.phar --ddm|--disdevmode -h | --help

Options:
    -h --help   Show this screen

USAGE;
    }
}
