<?php

class Magetools_Cache_Enable extends Magetools_Cache_Abstract
{
    protected function _process()
    {
        $this->_switch(1);
    }

    protected function _usageHelp()
    {
        return <<<USAGE
Magetools: Enable cache

Usage:
    mage.phar --mt|--modtree -a | --all
    mage.phar --mt|--modtree -t | --type cache_type
    mage.phar --mt|--modtree -h | --help

Options:
    -h --help Show this screen
    -a --all  Show all modules
    -t --type Type of cache
USAGE;
    }
}
