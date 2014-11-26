<?php

abstract class Magetools_Indexphp_Abstract extends Magetools_Abstract {

    protected $_opts = 'hi:';

    protected $_longOpts = array(
        'help', 'index:'
    );

    protected $_optsMap = array(
        'help' => 'h',
        'index' => 'i'
    );

    public function run()
    {
        try {
            $indexFile = $this->_getIndexFile();

            if (!is_writable($indexFile)) {
                throw new Exception(sprintf('Index.php "%s" is not writable', $indexFile));
            }

            $contents = file_get_contents($indexFile, FILE_TEXT);
            $this->_changeFileContents($contents);
            file_put_contents($indexFile, $contents, FILE_TEXT);

            $this->_printMessage(sprintf('Index.php file "%s" was updated successfully', $indexFile));
        } catch (Exception $e) {
            $this->_printMessage($e->getMessage(), true);
        }
    }

    abstract protected function _changeFileContents(&$contents);

    protected function _getIndexFile()
    {
        $indexFile = $this->_getOpt('index');

        if (isset($indexFile) && !empty($indexFile)) {
            $indexFile = $this->_getMageDir() . DS . $indexFile;
            if (!file_exists($indexFile)) {
                throw new Exception(sprintf('Index.php "%s" is not exists', $indexFile));
            }
            return $indexFile;
        }

        if (!$indexFile) {
            $indexFile = $this->_getMageDir() . DS . 'index.php';
            if (!file_exists($indexFile)) {
                throw new Exception(sprintf('Index.php "%s" is not exists', $indexFile));
            }
        }

        return $indexFile;
    }
}
