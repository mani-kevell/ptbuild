<?php

Namespace Controller ;

class SystemDetection extends Base {

    public function execute($pageVars) {

        $thisModel = new \Model\SystemDetectionAllOS($pageVars["route"]["extraParams"]);

        $isDefaultAction = parent::checkDefaultActions($pageVars, array(), $thisModel) ;
        if ( is_array($isDefaultAction) ) { return $isDefaultAction; }

        if ($pageVars["route"]["action"] === "detect") {
            $this->content["result"] = $thisModel;
            return array ("type"=>"view", "view"=>"systemDetection", "pageVars"=>$this->content); }

        if ($pageVars["route"]["action"] === "generate-defaults") {
            $thisModel = $this->getModelAndCheckDependencies(substr(get_class($this), 11), $pageVars, 'Generate') ;
            $this->content["data"] = $thisModel->generate();
            return array ("type"=>"view", "view"=>"systemDetection", "pageVars"=>$this->content); }

        $this->content["messages"][] = "Invalid Action";
        return array ("type"=>"control", "control"=>"index", "pageVars"=>$this->content);
    }

}