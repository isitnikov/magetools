<?php

class Magetools_Module_Disable extends Magetools_Module_Abstract
{
    protected function _process()
    {
        $force = $this->_getOpt('force');

        if (!$force && $this->_moduleStatus === false) {
            throw new Exception(sprintf('This module "%s" is already disabled', $this->_moduleName));
        }

        $dependsList = array();
        $this->_findDependsFromModule($this->_moduleName, $dependsList);

        if (count($dependsList)) {
            $enabled = array_filter($dependsList, function ($var) {
                return $var === true;
            });

            if (count($enabled)) {
                $this->_printMessage(sprintf(
                    'Cannot disable this module, because following modules "%s" is(are) depends from this',
                    implode('", "', array_keys($enabled))
                ));
                $this->_printMessage('Do you want to disable them together? [Y/n]');

                $line = trim(fgets(STDIN));
                if (in_array($line, array('y', 'Y'))) {
                    foreach ($enabled as $_moduleName => $status) {
                        if ($this->_changeModuleStatus($_moduleName, 'false')) {
                            $this->_printMessage(sprintf('Module "%s" was disabled successfully', $_moduleName));
                        } else {
                            $this->_printMessage(sprintf('Module "%s" wasn\'t disabled', $_moduleName));
                        }
                    }
                }
            }
        }

        $this->_moduleXml->modules->{$this->_moduleName}->active = 'false';

        if ($this->_moduleXml->saveXML($this->_modulePath)) {
            $this->_printMessage(sprintf('Module "%s" was disabled successfully', $this->_moduleName));
        } else {
            $this->_printMessage(sprintf('Module "%s" wasn\'t disabled', $this->_moduleName));
        }
    }

    protected function _usageHelp()
    {
        return <<<USAGE
Magetools: Disable specified module of Magento

Usage:
    mage.phar --dm|--dismod [-f|--force] Needed_Module
    mage.phar --dm|--dismod [-f|--force] -m|--module app/etc/modules/Needed_Module.xml
    mage.phar --dm|--dismod -h | --help

Options:
    -h --help   Show this screen
    -f --force  Force disabling (without checking for current status)
    -m --module Path to module declaration XML relative to Magento root folder

USAGE;
    }
}
