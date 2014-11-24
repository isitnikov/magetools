#!/usr/bin/env php
<?php
require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'abstract' . DIRECTORY_SEPARATOR . 'modules.abstract.php';

class Magetools_DisableModule extends Magetools_Modules_Abstract
{
    protected $_scriptName = 'magedismod.php';

    protected function _process()
    {
        $force = $this->_getOpt('force');

        if (!$force && $this->_moduleStatus === false) {
            throw new Exception(sprintf('This module "%s" is already disabled', $this->_moduleName));
        }

        $dependsList = array();
        $this->_findDependsFromModule($this->_moduleName, $dependsList);

        if (count($dependsList)) {
            $this->_printMessage(sprintf(
                'Cannot disable this module, because following modules "%s" is(are) depends from this',
                implode('", "', array_keys($dependsList))
            ));
            $this->_printMessage('Do you want to disable them together? [Y/n]');

            $line = trim(fgets(STDIN));
            if (in_array($line, array('y', 'Y'))) {
                foreach ($dependsList as $_moduleName => $status) {
                    if ($status === true) {
                        if ($this->_changeModuleStatus($_moduleName, 'false')) {
                            $this->_printMessage(sprintf('Module "%s" was disabled successfully', $_moduleName));
                        } else {
                            $this->_printMessage(sprintf('Module "%s" wasn\'t disabled', $_moduleName));
                        }
                    } else {
                        $this->_printMessage(sprintf('Module "%s" is already disabled', $_moduleName));
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
}

if (!defined('DO_NOT_RUN')) {
    $run = new Magetools_DisableModule();
    $run->run();
    exit(0);
}


//
//$options = getopt('hfm:', array(
//    'help', 'force', 'module:'
//));
//
//$moduleName = array_pop($argv);
//
//$actionsFile = actionsGetFileName('modules');
//$log = actionsGetLog($actionsFile);
//
//try {
//
//    if (isset($options['h']) || isset($options['help'])) {
//        help();
//    }
//
//
//    $magePath = getMagentoDir(getcwd());
//    chdir($magePath);
//
//    $modulesDir = $magePath . DS . 'app' . DS . 'etc' . DS . 'modules';
//
//    if (!is_writable($modulesDir)) {
//        throw new Exception('Modules dir is not writable');
//    }
//
//    $declarationFile = optionsGetModule($moduleName, $modulesDir, $options);
//
//    $force = optionsGetForce($options);
//
//    $declaration = simplexml_load_file($declarationFile);
//    if (!$declaration) {
//        throw new Exception(sprintf('Cannot load xml config of file "%s"', $declarationFile));
//    }
//
//    $moduleName = pathinfo($declarationFile, PATHINFO_FILENAME);
//
//    if (!$declaration->modules->xpath(sprintf('%s/active', $moduleName))) {
//        $children = $declaration->modules->children();
//        $moduleName = key($children);
//    }
//
//    $status = (string) $declaration->modules->$moduleName->active;
//
//    if (!$force && strtolower($status) === 'false') {
//        throw new Exception(sprintf('This module "%s" is already disabled', $moduleName));
//    }
//
//    $depends = isset($declaration->modules->$moduleName->depends)
//        ? $declaration->modules->$moduleName->depends->children()
//        : false;
//
//    if ($depends) {
//        $dependencies = getModulesDependencies($modulesDir);
//        if (count($dependencies)) {
//            $dependsFrom = array();
//            foreach ($dependencies as $_moduleName => $_config) {
//                if ($_config['active'] === true) {
//                    if (isset($_config['depends']) && in_array($moduleName, $_config['depends'])) {
//                        $dependsFrom[$_moduleName] = $_config['filename'];
//                    }
//                }
//            }
//
//            if ($dependsFrom) {
//                printMessage(sprintf(
//                    'Cannot enable this module, because following modules "%s" is(are) depends from this',
//                    implode('", "', array_keys($dependsFrom))
//                ));
//                printMessage('Do you want to disable them together? [Y/n]');
//
//                $line = trim(fgets(STDIN));
//                if (in_array($line, array('y', 'Y'))) {
//                    $log['disable'][$moduleName]['together'] = array();
//
//                    foreach ($dependsFrom as $_moduleName => $_xmlFileName) {
//                        $_xmlFileName = $modulesDir . DS . $_xmlFileName;
//                        if (!file_exists($_xmlFileName)) {
//                            printMessage(sprintf('Declaration file "%s" is not exists', $_xmlFileName));
//                            continue;
//                        }
//
//                        if (!is_writable($_xmlFileName)) {
//                            printMessage(sprintf('Declaration file "%s" is not writable', $_xmlFileName));
//                            continue;
//                        }
//
//                        $_declaration = simplexml_load_file($_xmlFileName);
//                        if (!$_declaration) {
//                            printMessage(sprintf('Cannot load xml config of file "%s"', $_xmlFileName));
//                            continue;
//                        }
//
//                        $_declaration->modules->$_moduleName->active = "false";
//
//                        if ($_declaration->saveXML($_xmlFileName)) {
////                            $log['disable'][$moduleName]['together'][$_moduleName] = $_xmlFileName;
//                        }
//                        printMessage(sprintf('Module "%s" was disabled successfully', $_moduleName));
//                    }
//                }
//            }
//        }
//    }
//
////    if (isset($log['enable']) && isset($log['enable'][$moduleName])) {
////        printMessage(sprintf(
////            'When you enable this module last time, then following %s modules also were enabled.',
////            implode(', ', array_keys($log['disable'][$moduleName]['together']))
////        ));
////        printMessage('Do you want to disable them too? [Y/n]');
////
////        $line = trim(fgets(STDIN));
////        if (in_array($line, array('y', 'Y'))) {
////            foreach ($log['enable'][$moduleName]['together'] as $_moduleName => $_xmlFileName) {
////                if (!file_exists($_xmlFileName)) {
////                    printMessage(sprintf('Declaration file "%s" is not exists', $_xmlFileName));
////                    continue;
////                }
////
////                if (!is_writable($_xmlFileName)) {
////                    printMessage(sprintf('Declaration file "%s" is not writable', $_xmlFileName));
////                    continue;
////                }
////
////                $_declaration = simplexml_load_file($_xmlFileName);
////                if (!$_declaration) {
////                    printMessage(sprintf('Cannot load xml config of file "%s"', $_xmlFileName));
////                    continue;
////                }
////
////                $_declaration->modules->$_moduleName->active = "false";
////
////                if ($_declaration->saveXML($_xmlFileName)) {
//////                    $log['enable'][$moduleName]['together'][$_moduleName] = $_xmlFileName;
////                }
////                printMessage(sprintf('Module "%s" was disabled successfully', $_moduleName));
////            }
////
////            unset($log['enable'][$moduleName]);
////        }
////    }
//
//
//    $declaration->modules->$moduleName->active = "false";
////
//    $declaration->saveXML($declarationFile);
//
//    printMessage(sprintf('Module "%s" was disabled successfully', $moduleName));
//
//    actionsSetLog($actionsFile, $log);
//    exit(0);
//}
//catch (Exception $e) {
//    printMessage($e->getMessage());
//}
//
//function help() {
//    $help = <<<HELP
//
//HELP;
//    exit(0);
//}