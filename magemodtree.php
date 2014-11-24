#!/usr/bin/env php
<?php
require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'abstract' . DIRECTORY_SEPARATOR . 'modules.abstract.php';

class Magetools_ModulesTree extends Magetools_Modules_Abstract
{
    protected $_opts = 'ham:';
    protected $_longOpts = array(
        'help', 'all', 'module:'
    );
    protected $_optsMap = array(
        'help'   => 'h',
        'all'    => 'a',
        'module' => 'm'
    );
    protected $_scriptName = 'magemodtree.php';

    protected function _initModule()
    {
        if ($this->_getOpt('all')) {
            return;
        }
        parent::_initModule();
    }

    protected function _process()
    {
        if ($this->_getOpt('all')) {
            foreach ($this->_dependencies as $moduleName => $info) {
                echo $this->_drawDependsTree($moduleName, 0);
            }
        } elseif (isset($this->_moduleName)) {
            echo $this->_drawDependsTree($this->_moduleName, 0);
        } else {
            $this->_showHelp();
        }
    }

    protected function _drawDependsTree($moduleName, $level = 0)
    {
        if (!isset($this->_dependencies[$moduleName]) || !($moduleConfig = $this->_dependencies[$moduleName])) {
            return '';
        }

        if (!isset($moduleConfig['depends']) || !count($moduleConfig['depends'])) {
            return '';
        }

        $str = '';

        if (!$level) {
            $str .= PHP_EOL;
            $str .= $moduleName . PHP_EOL;
            $str .= "Filename:\t" . $moduleConfig['filename'] . PHP_EOL;
            $str .= "Code pool:\t" . $moduleConfig['codePool'] . PHP_EOL;
            $str .= "Active:\t\t" . ($moduleConfig['active']
                ? $this->_getColoredValue('true', 'green')
                : $this->_getColoredValue('false', 'red')) . PHP_EOL;
            $str .= 'Dependencies: ' . PHP_EOL;
        }

        foreach ($moduleConfig['depends'] as $_moduleName) {
            $_moduleConfig = isset($this->_dependencies[$_moduleName])
                ? $this->_dependencies[$_moduleName]
                : array('active' => false);

            $str .= str_repeat("  ", $level + 1) .
                ($this->_getColoredValue($_moduleName, $_moduleConfig['active'] ? 'green' : 'red')) . PHP_EOL .
                $this->_drawDependsTree($_moduleName, $level + 2);
        }

        return $str;
    }
}

if (!defined('DO_NOT_RUN')) {
    $run = new Magetools_ModulesTree();
    $run->run();
    exit(0);
}
