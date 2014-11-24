#!/usr/bin/env php
<?php
require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'abstract' . DIRECTORY_SEPARATOR . 'indexphp.abstract.php';

class Magetools_DisableProfiler extends Magetools_IndexPhp_Abstract
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
}

if (!defined('DO_NOT_RUN')) {
    $run = new Magetools_DisableProfiler();
    $run->run();
    exit(0);
}
