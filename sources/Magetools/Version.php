<?php

class Magetools_Version extends Magetools_Abstract
{
    protected $_scriptName = 'mageversion.php';

    public function run()
    {
        try {
            $this->_loadAppMagePhp();
            $magentoEdition = '';
            if (method_exists('Mage', 'getEdition')) {
                $magentoEdition = sprintf("%s ", Mage::getEdition());
            }
            $this->_printMessage(sprintf('%s %s', $magentoEdition, Mage::getVersion()));
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
