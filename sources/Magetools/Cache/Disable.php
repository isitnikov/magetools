<?php

class Magetools_Cache_Disable extends Magetools_Cache_Abstract
{
    protected function _process()
    {
        $this->_switch(0);
    }
}
