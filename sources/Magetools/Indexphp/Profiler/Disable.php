<?php

class Magetools_Indexphp_Profiler_Disable extends Magetools_Indexphp_Abstract
{
    protected $_scriptName = 'magedisprof.php';

    protected function _changeFileContents(&$contents)
    {
        $patterns = array(
            '/(.*)(Varien\_Profiler\:\:enable.*)/m',
        );
        $replace = array(
            '#\\2'
        );

        $contents = preg_replace($patterns, $replace, $contents);

        $mageFile = $this->_getMageDir('app') . DS . 'Mage.php';

        if (!file_exists($mageFile)) {
            throw new Exception(sprintf('The main file of Magento "%s" is absent', $mageFile));
        }

        @require_once $mageFile;

        Mage::init();

        $config = new Mage_Core_Model_Config();
        $config->saveConfig('dev/debug/profiler', '0', 'default', Mage_Core_Model_App::ADMIN_STORE_ID);

        $this->_printMessage('Varien_Profiler is disabled');
    }

    protected function _usageHelp()
    {
        return <<<USAGE
Magetools: Disable Varien_Profiler

Usage:
    mage.phar --dp|--disprof
    mage.phar --dp|--disprof -h | --help

Options:
    -h --help   Show this screen

USAGE;
    }
}
