<?php

class Magetools_Cache_Enable extends Magetools_Cache_Abstract
{
    protected $_choiceNumbers = array();
    protected $_cacheTypes;

    protected function _process()
    {
        $cacheTypes = Mage::helper('core')->getCacheTypes();
        if ($this->_getOpt('all', false)) {
            $types = array();
            foreach ($cacheTypes as $_cacheType => $_value) {
                $types[$_cacheType] = 1;
            }
            Mage::app()->saveUseCache($types);
            $this->_printMessage('All types of cache were enabled.');
        } else {
            $type = $this->_getOpt('type', false);

            $clean = array();
            if ($type) {
                if (!isset($cacheTypes[$type])) {
                    throw new Exception(sprintf('Unknown type of cache: %s', $type));
                }
                $clean[$type] = 1;
            } else {
                $str = PHP_EOL;
                $str .= 'You don\'t set any specific params. Please, select one or few (by comma ",")'
                    . PHP_EOL . 'types of cache for enabling (press Enter to select all):';

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
                    $clean[$this->_choiceNumbers[$key]] = 1;
                }
            }

            if ($clean) {
                /**
                 * @TODO why it don't work?
                 */
                Mage::app()->saveUseCache($clean);
                $this->_printMessage(sprintf(
                    'Following cache types "%s" were enabled.',
                    implode('", "', array_keys($clean))
                ));
            } else {
                throw new Exception('You didn\'t select any exists cache types.');
            }
        }
    }

    protected function _walk(&$item, $key) {
        $item = 1;
    }

    protected function _filterChoice($var) {
        if (!isset($this->_choiceNumbers[$var])) {
            $this->_printMessage(sprintf('Choice "%d" was ignored, because not found among provided.', $var));
            return false;
        }
        return true;
    }
}
