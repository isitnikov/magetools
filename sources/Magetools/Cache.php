<?php

class Magetools_Cache extends Magetools_Cache_Abstract
{
    protected function _process()
    {
        $str = PHP_EOL;
        $str .= str_repeat('-', 80) . PHP_EOL;
        $str .= sprintf(
            '| %-49s| %-15s | %-7s |' . PHP_EOL,
            'Name', 'Type', 'Status'
        );
        $str .= str_repeat('-', 80) . PHP_EOL;
        foreach (Mage::helper('core')->getCacheTypes() as $type=>$label) {
            $str .= sprintf(
                '| %-49s| %-15s | %-7s |' . PHP_EOL,
                Mage::helper('adminhtml')->__($label), $type,
                (int)Mage::app()->useCache($type)
                    ? $this->_getColoredValue('Enabled', 'green')
                    : $this->_getColoredValue('Disabled', 'red')
            );
        }
        $str .= str_repeat('-', 80);
        $this->_printMessage($str);
    }
}
