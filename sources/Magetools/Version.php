<?php

class Magetools_Version extends Magetools_Abstract
{
    protected $_scriptName = 'mageversion.php';

    public function run()
    {
        try {
            $this->_loadAppMagePhp();
            if (class_exists('Mage')) {
                $this->_printMessage(sprintf('%s %s', Mage::getEdition(), Mage::getVersion()));
            } else {
                throw new Exception('Class "Mage" is not found. Seems this file hasn\'t permissions for reading.');
            }
        } catch (Exception $e) {
            $this->_printMessage($e->getMessage(), true);
        }
    }

    protected function _usageHelp()
    {
        return <<<USAGE
Magetools: Show version of Magento

Usage:
    mage.phar --v|--version
    mage.phar --v|--version -h | --help

Options:
    -h --help   Show this screen

USAGE;
    }
}
