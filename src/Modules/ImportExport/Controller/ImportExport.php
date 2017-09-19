<?php

Namespace Controller ;

class ImportExport extends Base {

     public function execute($pageVars) {

        $thisModel = $this->getModelAndCheckDependencies(substr(get_class($this), 11), $pageVars) ;
        if (is_array($thisModel)) { return $this->failDependencies($pageVars, $this->content, $thisModel) ; }

        $action = $pageVars["route"]["action"];

        if ($action === 'import') {
             $this->content["data"] = $thisModel->importJob();
             return array ("type"=>"view", "view"=>"importExport", "pageVars"=>$this->content); }

        if ($action=="help") {
            $helpModel = new \Model\Help();
            $this->content["helpData"] = $helpModel->getHelpData($pageVars["route"]["control"]);
            return array ("type"=>"view", "view"=>"help", "pageVars"=>$this->content); }

        $this->content["messages"][] = "Help is the only valid Import Export Action";
        return array ("type"=>"control", "control"=>"index", "pageVars"=>$this->content);

    }

}
