<?php

Namespace Model;

class BuilderCollaterAllOS extends Base {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("BuilderCollater") ;

    public function getBuilder($pipe = null) {
        if ($pipe != null) { $this->params["item"] = $pipe ; }
        $r = $this->collate();
        return $r ;
    }

    private function collate() {
        $collated = array() ;
        // $collated = array_merge($collated, $this->getStatuses()) ;
        $collated = array_merge($collated, $this->getBuildSteps()) ;
        // $collated = array_merge($collated, $this->getSteps()) ;
        return $collated ;
    }

    private function getStatuses() {
        $statuses = array( "last_status" => true, "has_parents" => true, "has_children" => true ) ;
        return $statuses ;
    }

    private function getBuildSteps() {
        $defaults = array();
        $defaultsFile = PIPEDIR.DS.$this->params["item"].DS.'defaults' ;
        if (file_exists($defaultsFile)) {
            $defaultsFileData =  file_get_contents($defaultsFile) ;
            $defaults = json_decode($defaultsFileData, true) ; }
        else {
            $loggingFactory = new \Model\Logging() ;
            $logging = $loggingFactory->getModel($this->params) ;
            $logging->log("No defaults file available in build", $this->getModuleName()) ; }
        $defaults = $this->setDefaultSlugIfNeeded($defaults) ;
        return $defaults ;
    }

    private function setDefaultSlugIfNeeded($defaults) {
        if (!isset($defaults["project-slug"])) {
            $defaults["project-slug"] = $this->params["item"] ; }
        if (isset($defaults["project-slug"]) && $defaults["project-slug"] == "") {
            $defaults["project-slug"] = $this->params["item"] ; }
        return $defaults ;
    }

    private function getSteps() {
        $statuses = array("steps" => array()) ;
        return $statuses ;
    }

}