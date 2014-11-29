<?php

class Magetools_Cache extends Magetools_Cache_Abstract
{
    protected $_opts = 'h';
    protected $_longOpts = array(
        'help'
    );
    protected $_optsMap = array(
        'help' => 'h'
    );
    protected function _process()
    {
        $str = PHP_EOL;
        $str .= str_repeat('-', 80) . PHP_EOL;
        $str .= sprintf(
            '%-50s| %-15s | %-8s' . PHP_EOL,
            'Name', 'Type', 'Status'
        );
        $str .= str_repeat('-', 80) . PHP_EOL;
        foreach (Mage::helper('core')->getCacheTypes() as $type=>$label) {
            $str .= sprintf(
                '%-50s| %-15s | %-8s' . PHP_EOL,
                Mage::helper('adminhtml')->__($label), $type,
                (int)Mage::app()->useCache($type)
                    ? $this->_getColoredValue('Enabled', 'green')
                    : $this->_getColoredValue('Disabled', 'red')
            );
        }
        $str .= str_repeat('-', 80);
        $this->_printMessage($str);
    }

    protected function _usageHelp()
    {
        return <<<USAGE
Magetools: Show cache information

Usage:
    mage.phar --mt|--modtree -h | --help

Options:
    -h --help Show this screen
    -a --all  Show all modules
    -t --type Type of cache
USAGE;
    }
}
