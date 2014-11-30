<?php

class Magetools_Cache_Clean extends Magetools_Cache_Abstract
{
    protected $_choiceNumbers = array();

    protected function _process()
    {
        if ($this->_getOpt('all', false)) {
            Mage::app()->cleanCache();
            $this->_printMessage('All types of cache were cleaned.');
        } else {
            $type = $this->_getOpt('type', false);
            $cacheTypes = Mage::helper('core')->getCacheTypes();

            $clean = array();
            if ($type) {
                if (!isset($cacheTypes[$type])) {
                    throw new Exception(sprintf('Unknown type of cache: %s', $type));
                }
                $clean[] = $type;
            } else {
                $str = PHP_EOL;
                $str .= 'You don\'t set any specific params. Please, select one or few (by comma ",")'
                    . PHP_EOL . 'types of cache for cleaning (press Enter to select all):';

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
                    $clean[] = $this->_choiceNumbers[$key];
                }
            }

            if ($clean) {
                /**
                 * @TODO why it don't work?
                 */
                Mage::app()->cleanCache($clean);
                $this->_printMessage(sprintf(
                    'Following cache types "%s" were cleaned.',
                    implode('", "', array_values($clean))
                ));
            } else {
                throw new Exception('You didn\'t select any exists cache types.');
            }
        }
    }

    protected function _usageHelp()
    {
        return <<<USAGE
Magetools: Clean cache

Usage:
    mage.phar --mt|--modtree -a | --all
    mage.phar --mt|--modtree -t | --type cache_type
    mage.phar --mt|--modtree -h | --help

Options:
    -h --help Show this screen
    -a --all  Show all modules
    -t --type Type of cache
USAGE;
    }
}
