<?php

Namespace Model;

class ModuleManagerWebActionAnyOS extends BasePHPApp {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("WebAction") ;

    public function getData() {
        $ret["mod_install"] = $this->installModules();
        return $ret ;
    }

    private function installModules() {
        var_dump($_REQUEST["module-source"]);
        if (isset($_REQUEST["module-source"]) && strlen($_REQUEST["module-source"]) > 0) {
            $modFactory = new \Model\ModuleManager() ;
            $mmpr = $this->params ;
            $mmpr["module-source"] = $_REQUEST["module-source"] ;
            $mm = $modFactory->getModel($mmpr);
            $mm->install(); }
        return true ;
    }

}