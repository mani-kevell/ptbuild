<?php

Namespace Controller ;

class BuildQueue extends Base {

    public function execute($pageVars) {

        $thisModel = $this->getModelAndCheckDependencies(substr(get_class($this), 11), $pageVars) ;
        if (is_array($thisModel)) {
            return $this->failDependencies($pageVars, $this->content, $thisModel) ; }

        $action = $pageVars["route"]["action"];

        if ($action === "findqueued") {
            // @todo output format change not being implemented
            $this->content["params"]["output-format"] = "JSON";
            $this->content["route"]["extraParams"]["output-format"] = "JSON";
            $this->content["data"] = $thisModel->findQueued();
            return array ("type"=>"view", "view"=>"buildQueueFindQueued", "pageVars"=>$this->content); }

        if (in_array($pageVars["route"]["action"], array("run-cycle"))) {
            $this->content["route"]["extraParams"]["output-format"] = "CLI";

            $thisModel = $this->getModelAndCheckDependencies(substr(get_class($this), 11), $pageVars, 'Consume') ;
            $this->content["data"] = $thisModel->getData();
            return array ("type"=>"view", "view"=>"buildQueueChild", "pageVars"=>$this->content); }

        if ($action === "help") {
            $helpModel = new \Model\Help();
            $this->content["helpData"] = $helpModel->getHelpData($pageVars["route"]["control"]);
            return array ("type"=>"view", "view"=>"help", "pageVars"=>$this->content); }

        $this->content["messages"][] = "Help is the only valid BuildQueue Action";
        return array ("type"=>"control", "control"=>"index", "pageVars"=>$this->content);

    }

}
