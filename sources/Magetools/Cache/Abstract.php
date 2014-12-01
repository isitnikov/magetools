<?php

abstract class Magetools_Cache_Abstract extends Magetools_Abstract
{
    protected $_opts = 'hat:';

    protected $_longOpts = array(
        'help', 'all', 'type:'
    );

    protected $_optsMap = array(
        'help'   => 'h',
        'all'    => 'a',
        'type'   => 't'
    );

    protected $_choiceNumbers = array();

    public function run()
    {
        try {
            $this->_loadAppMagePhp();
            Mage::app('admin')->setUseSessionInUrl(false);
            Mage::getConfig()->init();
            Mage::app('admin')->addEventArea('adminhtml');
            Mage::app('admin')->loadArea('adminhtml');
            $this->_process();
        } catch (Exception $e) {
            $this->_printMessage($e->getMessage(), true);
        }
    }

    abstract protected function _process();

    protected function _switch($status)
    {
        $cacheTypes = Mage::helper('core')->getCacheTypes();
        if ($this->_getOpt('all', false)) {
            $types = array();
            foreach ($cacheTypes as $_cacheType => $_value) {
                $types[$_cacheType] = (int)$status;
            }
            Mage::app()->saveUseCache($types);
            $this->_printMessage(sprintf(
                'All types of cache were %s.',
                $status ? 'enabled' : 'disabled'
            ));
        } else {
            $type = $this->_getOpt('type', false);

            $usedTypes = array();
            $clean = Mage::app()->useCache();
            if ($type) {
                if (!isset($cacheTypes[$type])) {
                    throw new Exception(sprintf('Unknown type of cache: %s', $type));
                }
                $clean[$type] = (int)$status;
                $usedTypes[] = $type;
            } else {
                $str = PHP_EOL;
                $str .= 'You don\'t set any specific params. Please, select one or few (by comma ",")'
                    . PHP_EOL . sprintf(
                        'types of cache for %s (press Enter to select all):',
                        $status ? 'enabling' : 'disabling'
                    );

                foreach ($cacheTypes as $_cacheType => $_label) {
                    $str .= PHP_EOL . sprintf(
                        '[%d] %s (%s)',
                        count($this->_choiceNumbers),
                        Mage::helper('adminhtml')->__($_label),
                        $_cacheType
                    );
                    $this->_choiceNumbers[] = (string)$_cacheType;
                }
                $this->_printMessage($str);
                $choice = trim(fgets(STDIN));
                if (empty($choice)) {
                    throw new Exception('Wrong choice. Good bye!');
                }

                /**
                 * @TODO refactor this
                 */
                $choice = array_filter(explode(',', $choice), array($this, '_filterChoice'));
                foreach ($choice as $key) {
                    $clean[$this->_choiceNumbers[$key]] = (int)$status;
                    $usedTypes[] = $this->_choiceNumbers[$key];
                }
            }

            if ($clean) {
                Mage::app()->saveUseCache($clean);
                $this->_printMessage(sprintf(
                    'Following cache types "%s" were %s.',
                    implode('", "', $usedTypes),
                    $status ? 'enabled' : 'disabled'
                ));
            } else {
                throw new Exception('You didn\'t select any exists cache types.');
            }
        }
    }

    protected function _filterChoice($var)
    {
        if (!isset($this->_choiceNumbers[$var])) {
            $this->_printMessage(sprintf('Choice "%d" was ignored, because not found among provided.', $var));
            return false;
        }
        return true;
    }
}
