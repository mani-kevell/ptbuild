<?php

Namespace Model;

class BuildListAllOS extends Base {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("Default") ;

    public function getData() {
        $ret["pipelines"] = $this->getPipelines();
        return $ret ;
    }

    public function getBuildStatus() {
        $pipeline = $this->getPipeline();
        $ret["build_status"] = $pipeline["history_index"][$this->params['run-id']]["status"] ;
        $ret["start"] = $pipeline["history_index"][$this->params['run-id']]["start"] ;
        $ret["end"] = $pipeline["history_index"][$this->params['run-id']]["end"] ;
        $ret["last_fail"] = $pipeline["last_fail"] ;
        $ret["last_success"] = $pipeline["last_success"] ;
        return $ret ;
    }

    public function getPipelines() {
        $pipelineFactory = new \Model\Pipeline() ;
        $pipeline = $pipelineFactory->getModel($this->params);
        $pipelines = $pipeline->getPipelines() ;
        return $pipelines ;
    }

    public function getPipeline() {
        $pipelineFactory = new \Model\Pipeline() ;
        $pipeline = $pipelineFactory->getModel($this->params);
        return $pipeline->getPipeline($this->params['item']);
    }

}