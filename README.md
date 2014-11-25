magetools
=========

Set of utilities for fast manage Magento 1.X

Goal
----
1. Simplify the debugging of Magento
2. Simplify the switching of modules which have a lot of dependencies and depends from other
3. Make the support of Magento more interesting and amusing yet :)

Requirements
------------
1. PHP 5.3+ (cli)
2. Unix-based OS

Installation
------------
1. Clone this repository locally 
2. Set permissions of *mage.php* for executing `chmod +x magetools/mage.php`
3. Link *mage.php* into your local *~/bin* folder ``ln -s `pwd`/magetools/mage.php ~/bin/mage.php``

How to use it?
--------------
Just run ``$ mage.php`` and you will get following usage infomation: 

````
Usage:
    mage.php
    mage.php -h | --help
    mage.php <command> [<args>]

Options:
    -h --help           Show this screen

The most commonly used mage commands are:
    --v | --version        Show Magento version
    --mt | --modtree       Show module(-s) dependencies tree
    --em | --enmod         Enable specified module
    --dm | --dismod        Disable specified module
    --edm | --endevmode    Enable MAGE_IS_DEVELOPER_MODE
    --ddm | --disdevmode   Disable MAGE_IS_DEVELOPER_MODE
    --ep | --enprof        Enable Varien_Profiler
    --dp | --disprof       Disable Varien_Profiler
    --esd | --ensqldebug   Enable SQL debug
    --dsd | --dissqldebug  Disable SQL debug
````
