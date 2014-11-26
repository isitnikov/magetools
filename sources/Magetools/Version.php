<?php

class Magetools_Version extends Magetools_Abstract
{
    protected $_scriptName = 'mageversion.php';

    public function run()
    {
        try {
            $mageFile = $this->_getMageDir('app') . DS . 'Mage.php';

            if (!file_exists($mageFile)) {
                throw new Exception(sprintf('The main file of Magento "%s" is absent', $mageFile));
            }

            @require_once $mageFile;

            $this->_printMessage(sprintf('%s %s', Mage::getEdition(), Mage::getVersion()));
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
