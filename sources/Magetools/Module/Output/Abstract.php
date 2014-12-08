<?php

abstract class Magetools_Module_Output_Abstract extends Magetools_Module_Abstract
{
    protected $_opts = 'hfm:s';

    protected $_longOpts = array(
        'help', 'force', 'module:', 'store'
    );

    protected $_optsMap = array(
        'help'   => 'h',
        'force'  => 'f',
        'module' => 'm',
        'store'  => 's'
    );

}
