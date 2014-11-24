<?php
require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'abstract.php';

abstract class Magetools_SqlDebug_Abstract extends Magetools_Abstract {

    public function run()
    {
        try {
            $adapterFile = $this->_getAdapterFile();

            $contents = file_get_contents($adapterFile, FILE_TEXT);
            $config = $this->_changeFileContents($contents);
            file_put_contents($adapterFile, $contents, FILE_TEXT);

            echo "Config:" . PHP_EOL;
            foreach ($config as $param => $value) {
                echo "$param: $value" . PHP_EOL;
            }
            $this->_printMessage(sprintf('Mysql adapter file "%s" was updated successfully', $adapterFile));
        } catch (Exception $e) {
            $this->_printMessage($e->getMessage(), true);
        }
    }

    abstract protected function _changeFileContents(&$contents);

    protected function _getAdapterFile()
    {
        $adapterFile = $this->_getMageDir('lib/Varien/Db/Adapter/Pdo') . DS . 'Mysql.php';

        if (!file_exists($adapterFile)) {
            throw new Exception(sprintf('Mysql adapter file "%s" is not exists', $adapterFile));
        }

        if (!is_writable($adapterFile)) {
            throw new Exception(sprintf('Mysql adapter file "%s" is not writable', $adapterFile));
        }

        return $adapterFile;
    }
}
