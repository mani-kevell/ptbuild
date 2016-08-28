<?php

Namespace Model;

class BuildHomeAllOS extends Base {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("Default") ;

    public function getData() {
        $ret["pipeline"] = $this->getPipeline();
        $ret["features"] = $this->getPipelineFeatures();
        $ret["historic_builds"] = $this->getOldBuilds();
        return $ret ;
    }

    public function deleteData() {
        $ret["pipeline"] = $this->deletePipeline();
        return $ret ;
    }

    public function getPipeline() {
        $pipelineFactory = new \Model\Pipeline() ;
        $pipeline = $pipelineFactory->getModel($this->params);
        $r = $pipeline->getPipeline($this->params["item"]);
        return $r ;
    }

    private function getOldBuilds() {
        $pdir = PIPEDIR.DS.$this->params["item"].DS.'history' ;
        $builds = scandir($pdir) ;
        $buildsRay = array();
        $limit = count($builds) ;
        $limit = ($limit < 10) ? $limit : 1 ;
        for ($i=0; $i < $limit; $i++) {
            if (!in_array($builds[$i], array(".", "..", "tmpfile"))){
                $buildsRay[] = $builds[$i] ; } }
        rsort($buildsRay) ;
        return $buildsRay ;
    }

    public function getPipelineFeatures() {
        $pipelineFactory = new \Model\Pipeline() ;
        $pipeline = $pipelineFactory->getModel($this->params);
        $r = $pipeline->getPipelineFeatures($this->params["item"]);
        return $r ;
    }

    public function deletePipeline() {
        $pipelineFactory = new \Model\Pipeline() ;
        $pipeline = $pipelineFactory->getModel($this->params);
        return $pipeline->deletePipeline($this->params["item"]);
    }

}