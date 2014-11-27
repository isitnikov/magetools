<?php

abstract class Magetools_Cache_Abstract extends Magetools_Abstract {

    protected $_opts = 'hat:';
    protected $_longOpts = array(
        'help', 'all', 'type:'
    );
    protected $_optsMap = array(
        'help'   => 'h',
        'all'    => 'a',
        'type'   => 't'
    );

    public function run()
    {
        try {
            $this->_loadAppMagePhp();
            Mage::app('admin')->setUseSessionInUrl(false);
            $this->_process();
        } catch (Exception $e) {
            $this->_printMessage($e->getMessage(), true);
        }
    }

    abstract protected function _process();
}
