#!/usr/bin/env php
<?php
require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'abstract' . DIRECTORY_SEPARATOR . 'indexphp.abstract.php';

class Magetools_EnableProfiler extends Magetools_IndexPhp_Abstract
{
    protected $_opts = 'h';
    protected $_longOpts = array(
        'help', 'ips:'
    );
    protected $_scriptName = 'mageenprof.php';

    protected function _changeFileContents(&$contents)
    {
        $patterns = array(
            '/(.*)(Varien\_Profiler\:\:enable.*)/m',
        );
        $replace = array(
            '\\2'
        );

        $contents = preg_replace($patterns, $replace, $contents);

        $mageFile = $this->_getMageDir('app') . DS . 'Mage.php';

        if (!file_exists($mageFile)) {
            throw new Exception(sprintf('The main file of Magento "%s" is absent', $mageFile));
        }

        @require_once $mageFile;

        Mage::init();

        $config = new Mage_Core_Model_Config();
        $config->saveConfig('dev/debug/profiler', '1', 'default', Mage_Core_Model_App::ADMIN_STORE_ID);
        $config->saveConfig(
            'dev/restrict/allow_ips', $this->_getOpt('ips'), 'default', Mage_Core_Model_App::ADMIN_STORE_ID
        );

        $this->_printMessage('Varien_Profiler is enabled');
    }

    protected function _usageHelp()
    {
        return <<<USAGE
Magetools: Enable Varien_Profiler

Usage:
    mage.php --ep|--enprof
    mage.php --ep|--enprof -h | --help
    mage.php --ep|--enprof --ips=<X.X.X.X>,...

Options:
    -h --help   Show this screen
    --ips       Comma-separated list of allowed IPs

USAGE;
    }
}
