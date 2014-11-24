#!/usr/bin/env php
<?php
require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'abstract' . DIRECTORY_SEPARATOR . 'indexphp.abstract.php';

class Magetools_EnableDevMode extends Magetools_IndexPhp_Abstract
{
    protected $_scriptName = 'mageendevmode.php';

    protected function _changeFileContents(&$contents)
    {
        $patterns = array(
            '/(if\s+\(isset\(\$_SERVER\[\'MAGE_IS_DEVELOPER_MODE\']\).+)(\R+)(.*)(\R+)(\}.*)/m',
            '/([#|\/]+\s*)(ini_set\(\'display_errors\'.*)/m'
        );
        $replace = array(
            '//\\1\\2\\3\\4//\\5',
            '\\2'
        );

        $contents = preg_replace($patterns, $replace, $contents);

        $this->_printMessage('MAGE_IS_DEVELOPER_MODE is enabled');
        $this->_printMessage("ini_set('display_errors'); is uncommented");
    }
}

if (!defined('DO_NOT_RUN')) {
    $run = new Magetools_EnableDevMode();
    $run->run();
    exit(0);
}
