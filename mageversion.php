#!/usr/bin/env php
<?php
require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'functions.php';

$options = getopt('h', array(
    'help'
));

try {
    if (isset($options['h']) || isset($options['help'])) {
        help();
    }

    $magePath = getMagentoDir(getcwd());
    chdir($magePath);

    @require 'app/Mage.php';

    if (class_exists('Mage')) {
        printMessage(sprintf('%s %s', Mage::getEdition(), Mage::getVersion()));
    } else {
        throw new Exception('Unable to load Mage class');
    }

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