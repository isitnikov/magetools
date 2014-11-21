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
    if ($die) {
        die(basename(__FILE__) . ': ' . $message . PHP_EOL);
    } else {
        echo basename(__FILE__) . ': ' . $message . PHP_EOL;
    }
}

function getMagentoDir($absolutePath) {
    if (!$absolutePath || $absolutePath === DS) {
        return false;
    }

    if (file_exists($absolutePath . DS . 'app' . DS. 'Mage.php')) {
        return $absolutePath;
    }

    return getMagentoDir(realpath($absolutePath . DS . '..' . DS));
}