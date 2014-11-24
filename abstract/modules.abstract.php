<?php
require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'abstract.php';

abstract class Magetools_Modules_Abstract extends Magetools_Abstract {

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
        $this->_getDependencies();
    }

    protected function _getDependencies()
    {
        if (!$this->_dependencies) {
            $this->_dependencies = array();

            $modulesDir = $this->_getMageDir('app/etc/modules');
            $modulesDeclarations = glob($modulesDir . DS . '*_*.xml');
            foreach ($modulesDeclarations as $moduleDeclaration) {
                if (!is_readable($moduleDeclaration)) {
                    continue;
                }

                $declarationXml = $this->_getModuleDeclarationXml($moduleDeclaration, false);

                if (!$declarationXml) {
                    continue;
                }

                foreach ($declarationXml->modules->children() as $moduleName => $options) {
                    $this->_dependencies[(string)$moduleName]['active'] = (string)$options->active === "true"
                        ? true : false;
                    $this->_dependencies[(string)$moduleName]['filename'] = basename($moduleDeclaration);
                    $this->_dependencies[(string)$moduleName]['codePool'] = (string)$options->codePool;

                    if (isset($options->depends) && count($options->depends->children())) {
                        foreach ($options->depends->children() as $_moduleName => $_v) {
                            $this->_dependencies[(string)$moduleName]['depends'][] = (string)$_moduleName;
                        }
                    }
                }
            }
        }

        return $this->_dependencies;
    }

    protected function _findDependsOfModule($moduleName, &$dependenciesList)
    {
        if (!isset($this->_dependencies[$moduleName]) || !($moduleConfig = $this->_dependencies[$moduleName])) {
            return;
        }

        if (!isset($moduleConfig['depends']) || !count($moduleConfig['depends'])) {
            return;
        }

        foreach ($moduleConfig['depends'] as $_moduleName) {
            $_moduleConfig = isset($this->_dependencies[$_moduleName])
                ? $this->_dependencies[$_moduleName]
                : array('active' => false);

            $dependenciesList[$_moduleName] = $_moduleConfig['active'];

            $this->_findDependsOfModule($_moduleName, $dependenciesList);
        }
    }

    protected function _findDependsFromModule($moduleName, &$dependenciesList)
    {
        foreach ($this->_dependencies as $_moduleName => $_moduleConfig) {
            if (!isset($_moduleConfig['depends']) || !count($_moduleConfig['depends'])) {
                continue;
            }

            if (in_array($moduleName, $_moduleConfig['depends'])) {
                $dependenciesList[$_moduleName] = $_moduleConfig['active'];
                $this->_findDependsFromModule($_moduleName, $dependenciesList);
            }
        }
    }

    protected function _changeModuleStatus($moduleName, $status)
    {
        if (!isset($this->_dependencies[$moduleName])) {
            return false;
        }

        $path = $this->_getMageDir('app/etc/modules') . DS . $this->_dependencies[$moduleName]['filename'];

        $declarationXml = $this->_getModuleDeclarationXml($path, false);

        if (!$declarationXml) {
            return false;
        }

        $declarationXml->modules->$moduleName->active = $status;

        return $declarationXml->saveXML($path);
    }

    protected function _initModule()
    {
        $modulesDir = $this->_getMageDir('app/etc/modules');

        if ($this->_getOpt('module')) {
            $this->_modulePath = $this->_getMageDir() . DS . $this->_getOpt('module');
            if (!file_exists($this->_modulePath)) {
                throw new Exception(sprintf('Declaration file "%s" is not exists', $this->_modulePath));
            }
        }

        if (!$this->_modulePath) {
            $this->_moduleName = $this->_getOpt('default');

            if (!preg_match('/^[A-z0-9]+\_[A-z0-9]+$/i', $this->_moduleName)) {
                throw new Exception('Name of custom module is absent');
            }

            $this->_modulePath = $modulesDir . DS . $this->_moduleName . '.xml';
            if (!file_exists($this->_modulePath)) {
                if (!isset($this->_dependencies[$this->_moduleName])) {
                    throw new Exception(sprintf('Declaration file "%s" is not exists', $this->_modulePath));
                } else {
                    $this->_modulePath = $modulesDir . DS . $this->_dependencies[$this->_moduleName]['filename'];
                }
            }
        }

        if (!is_readable($this->_modulePath) || !filesize($this->_modulePath)) {
            throw new Exception(sprintf('Declaration file "%s" is not readable or empty', $this->_modulePath));
        }

        $this->_moduleXml = $this->_getModuleDeclarationXml($this->_modulePath);

        if (!$this->_moduleName) {
            $modules = $this->_moduleXml->modules->children();
            if (count($modules) == 1) {
                $this->_moduleName = key($modules);
            } elseif (count($modules) > 1) {
                $this->_printMessage('In current module\'s declaration was found few modules. Please, select one by number (press Enter to select first one):');
                $chooseModule = array();
                foreach ($modules as $_moduleName => $_config) {
                    $this->_printMessage(sprintf('[%d] %s', count($chooseModule), $_moduleName));
                    $chooseModule[] = (string)$_moduleName;
                }
                $choice = (int)trim(fgets(STDIN));
                if (isset($chooseModule[$choice])) {
                    $this->_moduleName = $chooseModule[$choice];
                } else {
                    throw new Exception('Wrong choice. Good bye!');
                }
            } else {
                throw new Exception(sprintf('Described modules are absent in "%s"', $this->_modulePath));
            }
        }

        if (isset($this->_moduleXml->modules->{$this->_moduleName}->active)) {
            $this->_moduleStatus = (string)$this->_moduleXml->modules->{$this->_moduleName}->active === 'true'
                ? true : false;
        }
    }

    /**
     * @param $path
     * @param bool $throwException
     * @return bool|SimpleXMLElement
     * @throws Exception
     */
    protected function _getModuleDeclarationXml($path, $throwException = true)
    {
        $declarationXml = simplexml_load_file($path);
        if (!$declarationXml) {
            $message = sprintf('Declaration XML of "%s" cannot be parsed', $path);
            if ($throwException) {
                throw new Exception($message);
            } else {
                $this->_printMessage($message);
                return false;
            }
        }

        if (!$declarationXml->xpath('/config/modules')) {
            $message = sprintf('Node /config/modules in declaration XML "%s" is absent', $path);
            if ($throwException) {
                throw new Exception($message);
            } else {
                $this->_printMessage($message);
                return false;
            }
        }

        return $declarationXml;
    }

    public function run()
    {
        try {
            $this->_initModule();
            $this->_process();
        } catch (Exception $e) {
            $this->_printMessage($e->getMessage(), true);
        }
    }

    abstract protected function _process();
}
