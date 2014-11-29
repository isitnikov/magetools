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
2. Set permissions of *bin/mage.phar* for executing `chmod +x magetools/bin/mage.phar`
3. Link *mage.phar* into your local *~/bin* folder ``ln -s `pwd`/magetools/bin/mage.phar ~/bin/mage.phar``

How to use it?
--------------
Just run ``$ mage.phar`` and you will get following usage infomation: 

````
Usage:
    mage.phar
    mage.phar -h | --help
    mage.phar <command> [<args>]

Options:
    -h --help           Show this screen

The most commonly used mage commands are:
    --v | --version         Show Magento version
    --mt | --modtree        Show module(-s) dependencies tree
    --em | --enmod          Enable specified module
    --dm | --dismod         Disable specified module
    --edm | --endevmode     Enable MAGE_IS_DEVELOPER_MODE
    --ddm | --disdevmode    Disable MAGE_IS_DEVELOPER_MODE
    --ep | --enprof         Enable Varien_Profiler
    --dp | --disprof        Disable Varien_Profiler
    --esd | --ensqldebug    Enable SQL debug
    --dsd | --dissqldebug   Disable SQL debug
    --c | --cache           Show cache information
    --cc | --cache-clean    Clean cache
    --ce | --cache-enable   Enable cache
    --cd | --cache-disable  Disable cache
````
