#!/usr/bin/env php
<?php
require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'functions.php';

$options = getopt('hfm:', array(
    'help', 'force', 'module:'
));

$moduleName = array_pop($argv);

try {
    if (isset($options['h']) || isset($options['help'])) {
        help();
    }

    if (!($magePath = getMagentoDir(getcwd()))) {
        throw new Exception('You should run this script inside Magento folder (or its childs)');
    }

    $modulesDir = $magePath . DS . 'app' . DS . 'etc' . DS . 'modules';

    if (!is_writable($modulesDir)) {
        throw new Exception('Modules dir is not writable');
    }

    chdir($magePath);

    $declarationFile = false;

    if (isset($options['m']) && !empty($options['m'])) {
        if (!file_exists($options['m'])) {
            throw new Exception(sprintf('Declaration file "%s" is not exists', $options['m']));
        }
        $declarationFile = $options['m'];
    }

    if (isset($options['module']) && !empty($options['module'])) {
        if (!file_exists($options['module'])) {
            throw new Exception(sprintf('Declaration file "%s" is not exists', $options['module']));
        }
        $declarationFile = $options['module'];
    }

    if (!$declarationFile) {
        if (!preg_match('/^[A-z0-9]+\_[A-z0-9]+$/i', $moduleName)) {
            throw new Exception('Name of custom module is absent');
        }

        $declarationFile = $modulesDir . DS . $moduleName . '.xml';
        if (!file_exists($declarationFile)) {
            throw new Exception(sprintf('Declaration file "%s" is not exists', $declarationFile));
        }
    }

    $force = (isset($options['f']) || isset($options['force']));

    if (!is_readable($declarationFile) || !filesize($declarationFile)) {
        throw new Exception(sprintf('Declaration file "%s" is not readable or empty', $declarationFile));
    }

    $declaration = simplexml_load_file($declarationFile);
    if (!$declaration) {
        throw new Exception(sprintf('Cannot load xml config of file "%s"', $declarationFile));
    }

    $moduleName = pathinfo($declarationFile, PATHINFO_FILENAME);

    $status = (string) $declaration->modules->$moduleName->active;

    if (strtolower($status) === 'true') {
        throw new Exception(sprintf('This module "%s" is already enabled', $moduleName));
    }
//
//
//    $modulesDeclarations = glob($currentDir . DIRECTORY_SEPARATOR . 'app/etc/modules/*.xml');
//    foreach ($modulesDeclarations as $declarationFile) {

//
//        foreach ($declaration->modules->children() as $moduleName => $options) {
//            if (in_array($moduleName, $excludeModules)) {
//                continue;
//            }
//            if ((string)$options->active === 'true' && (string)$options->codePool !== "core") {
//                $usedModules[strtolower($moduleName)] = $moduleName;
//            }
//        }
//    }

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