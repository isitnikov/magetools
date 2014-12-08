<?php

class Magetools_Module_Output_Status extends Magetools_Module_Output_Abstract
{
    protected $_opts = 'ham:s';

    protected $_longOpts = array(
        'help', 'all', 'module:', 'store'
    );
    protected $_optsMap = array(
        'help'   => 'h',
        'all'    => 'a',
        'module' => 'm',
        'store'  => 's'
    );

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

            if ($this->_getOpt('depends')) {
                $dependsOf = array();
                $this->_findDependsFromModule($this->_moduleName, $dependsOf);

                if (count($dependsOf)) {
                    echo PHP_EOL . 'Modules which are depends from this module:';

                    foreach ($dependsOf as $_moduleName => $_v) {
                        echo $this->_drawDependsTree($_moduleName, 0);
                    }
                }
            }
        } else {
            $this->_showHelp();
        }
    }

    protected function _drawDependsTree($moduleName, $level = 0)
    {
        if (!isset($this->_dependencies[$moduleName]) || !($moduleConfig = $this->_dependencies[$moduleName])) {
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
            if (!isset($moduleConfig['depends']) || !count($moduleConfig['depends'])) {
                $str .= PHP_EOL . '(Node <depends> is absent or empty.)' . PHP_EOL;
                return $str;
            }
        }

        if (!isset($moduleConfig['depends']) || !count($moduleConfig['depends'])) {
            return '';
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

    protected function _usageHelp()
    {
        return <<<USAGE
Magetools: Show module(-s) dependencies tree

Usage:
    mage.phar --mt|--modtree -a | --all
    mage.phar --mt|--modtree [-d]-m | [--depends]--module app/etc/modules/Needed_Module.xml
    mage.phar --mt|--modtree -h | --help

Options:
    -h --help    Show this screen
    -a --all     Show all modules
    -d --depends Show modules which are depends from this module
    -m --module Path to module declaration XML relative to Magento root folder

USAGE;
    }
}
