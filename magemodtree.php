#!/usr/bin/env php
<?php
require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'functions.php';

$options = getopt('ham:', array(
    'help', 'all', 'module:', 'active:'
));

$moduleName = array_pop($argv);

try {
    if (isset($options['h']) || isset($options['help'])) {
        help();
    }

    $magePath = getMagentoDir(getcwd());

    chdir($magePath);

    $modulesDir = $magePath . DS . 'app' . DS . 'etc' . DS . 'modules';

    $showAll = isset($options['a']) || isset($options['all']);
    $showActive = isset($options['active']);

    try {
        $declarationFile = optionsGetModule($moduleName, $modulesDir, $options);
    } catch (Exception $e) {
        $declarationFile = false;
    }

    $dependencies = getModulesDependencies($modulesDir);
//var_dump($dependencies['Mana_Seo']);
    if ($declarationFile) {
        $moduleName = pathinfo($declarationFile, PATHINFO_FILENAME);

        if (isset($dependencies[$moduleName])) {
            if (!isset($dependencies[$moduleName]['depends'])) {
                printMessage(sprintf('Module "%s" has not any dependencies', $moduleName));
            } else {
                echo moduleGetDependsTree($moduleName, $dependencies, 0, $showActive);
            }
        } else {
            throw new Exception(sprintf('Module "%s" is absent', $moduleName));
        }
    }

    elseif (preg_match('/^[A-z0-9]+\_[A-z0-9]+$/i', $moduleName)) {
        if (isset($dependencies[$moduleName])) {
            if (!isset($dependencies[$moduleName]['depends'])) {
                printMessage(sprintf('Module "%s" has not any dependencies', $moduleName));
            } else {
                echo moduleGetDependsTree($moduleName, $dependencies, 0, $showActive);
            }
        } else {
            throw new Exception(sprintf('Module "%s" is absent', $moduleName));
        }
    }

    elseif ($showAll === true) {
        foreach ($dependencies as $moduleName => $info) {
            echo moduleGetDependsTree($moduleName, $dependencies, 0, $showActive);
        }
    }

    else {
        help();
    }

    exit(0);
}
catch (Exception $e) {
    printMessage($e->getMessage(), true);
}

function help() {
    $help = <<<HELP
read help

HELP;
    echo $help;
    exit(0);
}