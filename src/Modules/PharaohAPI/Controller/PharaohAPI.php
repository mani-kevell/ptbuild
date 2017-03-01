<?php

Namespace Controller ;

class PharaohAPI extends Base {

     public function execute($pageVars) {

         $thisModel = $this->getModelAndCheckDependencies(substr(get_class($this), 11), $pageVars) ;
         if (is_array($thisModel)) {
             return $this->failDependencies($pageVars, $this->content, $thisModel) ; }

         if ($thisModel->keyIsAllowedAccess() !== true) {
             $override = $this->getIndexControllerForOverride() ;
             return $override->execute() ; }

         $action = $pageVars["route"]["action"];

         if ($pageVars["route"]["action"] === "call") {
             $this->content["data"] = $thisModel->getReportData() ;
             return array ("type"=>"view", "view"=>"pharaohAPI", "pageVars" => $this->content) ; }

         if ($action === 'help') {
             $helpModel = new \Model\Help();
             $this->content["helpData"] = $helpModel->getHelpData($pageVars['route']['control']);
             return array ("type"=>"view", "view"=>"help", "pageVars"=>$this->content); }

         $this->content["messages"][] = "Invalid HTML reports Action";
         return array ("type"=>"control", "control"=>"index", "pageVars"=>$this->content);

     }

    protected function getIndexControllerForOverride() {
        return \Core\AutoLoader::getController("Signup")  ;
    }


}
