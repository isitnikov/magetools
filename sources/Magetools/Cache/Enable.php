<?php

class Magetools_Cache_Enable extends Magetools_Cache_Abstract
{
    protected function _process()
    {
        $this->_switch(1);
    }
}
