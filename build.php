<?php
chdir(getcwd() . DIRECTORY_SEPARATOR . 'sources');

$dirIterator = new RecursiveDirectoryIterator(realpath("Magetools"));
$iterator    = new RecursiveIteratorIterator($dirIterator, RecursiveIteratorIterator::SELF_FIRST);
$files       = array(
    'mage.php' => realpath('mage.php')
);
/** @var SplFileInfo $file */
foreach ($iterator as $file) {
    if (is_file($file->getPathname())) {
        $files[str_replace(realpath(".") . DIRECTORY_SEPARATOR, '', $file->getPathname())] = $file->getPathname();
    }
}

chdir(realpath('../bin'));

try {
    $pharFile = 'mage.phar';
    unlink($pharFile);
    $phar = new Phar($pharFile);
    $phar->buildFromIterator(new ArrayIterator($files));
    $phar->setStub(createStub());

    shell_exec('chmod +x ' . $pharFile);
} catch (Exception $e) {
    die($e->getMessage());
}
echo 'Phar archive ' . $pharFile . ' was created successfully.' . PHP_EOL;
exit(0);

function createStub()
{
    $stub = <<<ENDSTUB
#!/usr/bin/env php
<?php
Phar::mapPhar('mage.phar');
require 'phar://mage.phar/mage.php';
__HALT_COMPILER();
ENDSTUB;

    return $stub;
}