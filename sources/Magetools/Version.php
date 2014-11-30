<?php

class Magetools_Version extends Magetools_Abstract
{
    public function run()
    {
        try {
            $this->_loadAppMagePhp();
            $magentoVersion = Mage::getVersion();
            if (method_exists('Mage', 'getEdition')) {
                $magentoEdition = sprintf("%s ", Mage::getEdition());
            } else {
                $magentoEdition = version_compare($magentoVersion, '1.7.0.0', '<') ? 'Community' : 'Enterprise';
            }
            $this->_printMessage(sprintf('%s %s', $magentoEdition, $magentoVersion));
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
