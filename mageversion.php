#!/usr/bin/env php
<?php
require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'abstract' . DIRECTORY_SEPARATOR . 'abstract.php';

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
}

if (!defined('DO_NOT_RUN')) {
    $run = new Magetools_Version();
    $run->run();
    exit(0);
}
