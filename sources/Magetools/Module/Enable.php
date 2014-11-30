<?php

class Magetools_Module_Enable extends Magetools_Module_Abstract
{
    protected function _process()
    {
        $force = $this->_getOpt('force');

        if (!$force && $this->_moduleStatus === true) {
            throw new Exception(sprintf('This module "%s" is already enabled', $this->_moduleName));
        }

        $dependsList = array();
        $this->_findDependsOfModule($this->_moduleName, $dependsList);

        if (count($dependsList)) {
            $disabled = array_filter($dependsList, function ($var) {
                return $var === false;
            });

            if (count($disabled)) {
                $this->_printMessage(sprintf(
                    'Cannot enable this module, because following dependencies "%s" is(are) disabled',
                    implode('", "', array_keys($disabled))
                ));
                $this->_printMessage('Do you want to enable them together? [Y/n]');

                $line = trim(fgets(STDIN));
                if (in_array($line, array('y', 'Y'))) {
                    foreach ($disabled as $_moduleName => $status) {
                        if ($this->_changeModuleStatus($_moduleName, 'true')) {
                            $this->_printMessage(sprintf('Module "%s" was enabled successfully', $_moduleName));
                        } else {
                            $this->_printMessage(sprintf('Module "%s" wasn\'t enabled', $_moduleName));
                        }
                    }
                }
            }
        }

        $this->_moduleXml->modules->{$this->_moduleName}->active = 'true';

        if ($this->_moduleXml->saveXML($this->_modulePath)) {
            $this->_printMessage(sprintf('Module "%s" was enabled successfully', $this->_moduleName));
        } else {
            $this->_printMessage(sprintf('Module "%s" wasn\'t enabled', $this->_moduleName));
        }
    }

    protected function _usageHelp()
    {
        return <<<USAGE
Magetools: Enable specified module of Magento

Usage:
    mage.phar --em|--enmod [-f|--force] Needed_Module
    mage.phar --em|--enmod [-f|--force] -m|--module app/etc/modules/Needed_Module.xml
    mage.phar --em|--enmod -h | --help

Options:
    -h --help   Show this screen
    -f --force  Force disabling (without checking for current status)
    -m --module Path to module declaration XML relative to Magento root folder

USAGE;
    }
}
