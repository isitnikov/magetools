#!/usr/bin/env php
<?php
require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'functions.php';

$options = getopt('hfm:', array(
    'help', 'force', 'module:'
));

$moduleName = array_pop($argv);

$actionsFile = actionsGetFileName('modules');
$log = actionsGetLog($actionsFile);

try {
    if (isset($options['h']) || isset($options['help'])) {
        help();
    }


    $magePath = getMagentoDir(getcwd());
    chdir($magePath);

    $modulesDir = $magePath . DS . 'app' . DS . 'etc' . DS . 'modules';

    if (!is_writable($modulesDir)) {
        throw new Exception('Modules dir is not writable');
    }

    $declarationFile = optionsGetModule($moduleName, $modulesDir, $options);

    $force = optionsGetForce($options);

    $declaration = simplexml_load_file($declarationFile);
    if (!$declaration) {
        throw new Exception(sprintf('Cannot load xml config of file "%s"', $declarationFile));
    }

    $moduleName = pathinfo($declarationFile, PATHINFO_FILENAME);

    if (!$declaration->modules->xpath(sprintf('%s/active', $moduleName))) {
        $children = $declaration->modules->children();
        $moduleName = key($children);
    }

    $status = (string) $declaration->modules->$moduleName->active;

    if (strtolower($status) === 'true') {
        throw new Exception(sprintf('This module "%s" is already enabled', $moduleName));
    }

    $depends = isset($declaration->modules->$moduleName->depends)
        ? $declaration->modules->$moduleName->depends->children()
        : false;

    if ($depends) {
        $dependenciesList = array();
        $dependencies = getModulesDependencies($modulesDir);
        moduleGetDepends($moduleName, $dependencies, $dependenciesList);

        if (count($dependenciesList) && in_array(false, $dependenciesList)) {
            $disabled = array();
            foreach ($dependenciesList as $_moduleName => $_status) {
                if ($_status === false) {
                    $disabled[$_moduleName] = $dependencies[$_moduleName]['filename'];
                }
            }

            if ($disabled) {
                printMessage(sprintf(
                    'Cannot enable this module, because it depended from modules "%s" which is(are) disabled',
                    implode('", "', array_keys($dependsFrom))
                ));
                printMessage('Do you want to enable them too? [Y/n]');

                $line = trim(fgets(STDIN));
                if (in_array($line, array('y', 'Y'))) {
                    $log['enable'][$moduleName]['together'] = array();

                    foreach ($dependsFrom as $_moduleName => $_xmlFileName) {
                        $_xmlFileName = $modulesDir . DS . $_xmlFileName;
                        if (!file_exists($_xmlFileName)) {
                            printMessage(sprintf('Declaration file "%s" is not exists', $_xmlFileName));
                            continue;
                        }

                        if (!is_writable($_xmlFileName)) {
                            printMessage(sprintf('Declaration file "%s" is not writable', $_xmlFileName));
                            continue;
                        }

                        $_declaration = simplexml_load_file($_xmlFileName);
                        if (!$_declaration) {
                            printMessage(sprintf('Cannot load xml config of file "%s"', $_xmlFileName));
                            continue;
                        }

                        $_declaration->modules->$_moduleName->active = "true";

                        if ($_declaration->saveXML($_xmlFileName)) {
//                            $log['enable'][$moduleName]['together'][$_moduleName] = $_xmlFileName;
                        }
                        printMessage(sprintf('Module "%s" was disabled successfully', $_moduleName));
                    }
                }
            }
        }
    }

//    if (isset($log['disable']) && isset($log['disable'][$moduleName])) {
////        var_dump($log['disable'][$moduleName]['together']);
////        exit;
//        printMessage(sprintf(
//            'When you disable this module last time, then following %s modules also were disabled.',
//            implode(', ', array_keys($log['disable'][$moduleName]['together']))
//        ));
//        printMessage('Do you want to enable them too? [Y/n]');
//
//        $line = trim(fgets(STDIN));
//        if (in_array($line, array('y', 'Y'))) {
//            foreach ($log['disable'][$moduleName]['together'] as $_moduleName => $_xmlFileName) {
//                if (!file_exists($_xmlFileName)) {
//                    printMessage(sprintf('Declaration file "%s" is not exists', $_xmlFileName));
//                    continue;
//                }
//
//                if (!is_writable($_xmlFileName)) {
//                    printMessage(sprintf('Declaration file "%s" is not writable', $_xmlFileName));
//                    continue;
//                }
//
//                $_declaration = simplexml_load_file($_xmlFileName);
//                if (!$_declaration) {
//                    printMessage(sprintf('Cannot load xml config of file "%s"', $_xmlFileName));
//                    continue;
//                }
//
//                $_declaration->modules->$_moduleName->active = "true";
//
//                if ($_declaration->saveXML($_xmlFileName)) {
//                    $log['enable'][$moduleName]['together'][$_moduleName] = $_xmlFileName;
//                }
//                printMessage(sprintf('Module "%s" was enabled successfully', $_moduleName));
//            }
//
//            unset($log['disable'][$moduleName]);
//        }
//    }

    $declaration->modules->$moduleName->active = "true";

    $declaration->saveXML($declarationFile);

    printMessage(sprintf('Module "%s" was enabled successfully', $moduleName));

    actionsSetLog($actionsFile, $log);

    exit(0);
}
catch (Exception $e) {
    printMessage($e->getMessage(), true);
}

function help() {
    $help = <<<HELP

HELP;
    exit(0);
}