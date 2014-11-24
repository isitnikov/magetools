#!/usr/bin/env php
<?php
require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'abstract' . DIRECTORY_SEPARATOR . 'modules.abstract.php';

class Magetools_EnableModule extends Magetools_Modules_Abstract
{
    protected $_scriptName = 'mageenmod.php';

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

        $this->_moduleXml->modules->{$this->_moduleName}->active = 'true';

        if ($this->_moduleXml->saveXML($this->_modulePath)) {
            $this->_printMessage(sprintf('Module "%s" was enabled successfully', $this->_moduleName));
        } else {
            $this->_printMessage(sprintf('Module "%s" wasn\'t enabled', $this->_moduleName));
        }
    }
}

if (!defined('DO_NOT_RUN')) {
    $run = new Magetools_EnableModule();
    $run->run();
    exit(0);
}
