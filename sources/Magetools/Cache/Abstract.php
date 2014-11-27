<?php

abstract class Magetools_Cache_Abstract extends Magetools_Abstract {

    protected $_opts = 'hfm:';
    protected $_longOpts = array(
        'help', 'force', 'module:'
    );
    protected $_optsMap = array(
        'help'   => 'h',
        'force'  => 'f',
        'module' => 'm'
    );

    protected $_dependencies;

    protected $_moduleName;
    protected $_modulePath;
    /** @var null|SimpleXMLElement */
    protected $_moduleXml;
    protected $_moduleStatus;

    public function __construct()
    {
        parent::__construct();
    }

    public function run()
    {
        try {
            $this->_process();
        } catch (Exception $e) {
            $this->_printMessage($e->getMessage(), true);
        }
    }

    abstract protected function _process();
}
