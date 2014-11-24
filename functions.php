<?php
define('DS' , DIRECTORY_SEPARATOR);

function checkDomain ($domain, $port = 80)
{
    $file = @fsockopen($domain, $port, $errno, $errstr, 5);

    if (!$file) {
        return false;
    }

    fclose($file);

    return true;
}

function printMessage ($message, $die = false)
{
    $name = basename(__FILE__);
    $debug = debug_backtrace();
    if (isset($debug[0])) {
        $name = basename($debug[0]['file']);
    }
    if ($die) {
        die($name . ': ' . $message . PHP_EOL);
    } else {
        echo $name . ': ' . $message . PHP_EOL;
    }
}

/**
 * @param $absolutePath
 * @return mixed
 * @throws Exception
 */
function getMagentoDir($absolutePath)
{
    if (!$absolutePath || $absolutePath === DS) {
        throw new Exception('You should run this script inside Magento folder (or its childs)');
    }

    if (file_exists($absolutePath . DS . 'app' . DS. 'Mage.php')) {
        return $absolutePath;
    }

    return getMagentoDir(realpath($absolutePath . DS . '..' . DS));
}

function getModulesDependencies($modulesDir)
{
    $dependencies = array();

    $modulesDeclarations = glob($modulesDir . DS . '*_*.xml');

    foreach ($modulesDeclarations as $moduleDeclaration) {
        if (!is_readable($moduleDeclaration)) {
            continue;
        }

        $declarationXml = moduleGetDeclarationXml($moduleDeclaration, false);

        foreach ($declarationXml->modules->children() as $moduleName => $options) {
//            if ($moduleName == 'ManaPro_FilterAdmin') {
//                var_dump($options);
//                exit;
//            }
            $dependencies[(string)$moduleName]['active'] = (string)$options->active === "true" ? true : false;
            $dependencies[(string)$moduleName]['filename'] = basename($moduleDeclaration);
            if (isset($options->depends) && count($options->depends->children())) {
                foreach ($options->depends->children() as $_moduleName => $_v) {
                    $dependencies[(string)$moduleName]['depends'][] = (string)$_moduleName;
                }
            }
        }
    }

    return $dependencies;
}

function optionsGetModule($moduleName, $modulesDir, $options)
{
    $declarationFile = false;

    if (isset($options['m']) && !empty($options['m'])) {
        if (!file_exists($options['m'])) {
            throw new Exception(sprintf('Declaration file "%s" is not exists', $options['m']));
        }
        $declarationFile = $options['m'];
    }

    if (!$declarationFile && isset($options['module']) && !empty($options['module'])) {
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

    if (!is_readable($declarationFile) || !filesize($declarationFile)) {
        throw new Exception(sprintf('Declaration file "%s" is not readable or empty', $declarationFile));
    }

    return $declarationFile;
}

function optionsGetForce($options)
{
    return (isset($options['f']) || isset($options['force']));
}

/**
 * @param $path
 * @param bool $throwException
 * @return SimpleXMLElement
 * @throws Exception
 */
function moduleGetDeclarationXml($path, $throwException = true)
{
    $declarationXml = simplexml_load_file($path);
    if (!$declarationXml) {
        $message = sprintf('Declaration XML of %s cannot be parsed', basename($path));
        if ($throwException) {
            throw new Exception($message);
        } else {
            printMessage($message);
        }
    }

    return $declarationXml;
}

function moduleGetDependsTree($moduleName, $dependencies, $level = 0, $showActive = false)
{
    if (!isset($dependencies[$moduleName]) || !($moduleConfig = $dependencies[$moduleName])) {
        return '';
    }

    if (!isset($moduleConfig['depends']) || !count($moduleConfig['depends'])) {
        return '';
    }

    $str = '';

    if (!$level) {
        $str .= '---' . PHP_EOL;
        $str .= $moduleName . PHP_EOL;
        $str .= "Filename:\t" . $moduleConfig['filename'] . PHP_EOL;
        $str .= "Active:\t\t" . ($moduleConfig['active'] ? returnColoredValue('true', 'green') : returnColoredValue('false', 'red')) . PHP_EOL;
        $str .= 'Dependencies: ' . PHP_EOL;
    }

    foreach ($moduleConfig['depends'] as $_moduleName) {
        if ($showActive) {
            if ($moduleConfig['active'] == false) {
                continue;
            }
        }

        $_moduleConfig = isset($dependencies[$_moduleName]) ? $dependencies[$_moduleName] : array('active' => false);

        $str .= str_repeat("  ", $level + 1) .
            (returnColoredValue($_moduleName, (bool)$_moduleConfig['active'] ? 'green' : 'red')) . PHP_EOL .
            moduleGetDependsTree($_moduleName, $dependencies, $level + 2, $showActive);
    }

    return $str;
}

function moduleGetDepends($moduleName, $dependencies, &$dependenciesList)
{
    if (!isset($dependencies[$moduleName]) || !($moduleConfig = $dependencies[$moduleName])) {
        return;
    }

    if (!isset($moduleConfig['depends']) || !count($moduleConfig['depends'])) {
        return;
    }

    foreach ($moduleConfig['depends'] as $_moduleName) {
        $_moduleConfig = isset($dependencies[$_moduleName]) ? $dependencies[$_moduleName] : array('active' => false);

        $dependenciesList[$_moduleName] = $_moduleConfig['active'];

        moduleGetDepends($_moduleName, $dependencies, $dependenciesList);
    }
}

function returnColoredValue($str, $color = 'white')
{
    $colors['black'] = '0;30';
    $colors['dark_gray'] = '1;30';
    $colors['blue'] = '0;34';
    $colors['light_blue'] = '1;34';
    $colors['green'] = '0;32';
    $colors['light_green'] = '1;32';
    $colors['cyan'] = '0;36';
    $colors['light_cyan'] = '1;36';
    $colors['red'] = '0;31';
    $colors['light_red'] = '1;31';
    $colors['purple'] = '0;35';
    $colors['light_purple'] = '1;35';
    $colors['brown'] = '0;33';
    $colors['yellow'] = '1;33';
    $colors['light_gray'] = '0;37';
    $colors['white'] = '1;37';
    return "\033[" . $colors[$color] . "m" . $str ."\033[0m";
}

function actionsGetFileName($suffix)
{
    $filename = sys_get_temp_dir() . DS . get_current_user() . DS . $suffix . '-actions.magetools.php';
    if (!is_dir(dirname($filename))) {
        mkdir(dirname($filename));
    }

    if (!file_exists($filename)) {
        touch($filename);
    }

    return $filename;
}

function actionsGetLog($filename)
{
    $actions = array();

    if (file_exists($filename)) {
        require_once $filename;
    }

    return $actions;
}

function actionsSetLog($filename, $log) {
    file_put_contents($filename, sprintf('<?php $actions = %s;', var_export($log, true)));
}