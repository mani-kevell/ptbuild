<?php

Namespace Controller ;

class PublishReleases extends Base {

     public function execute($pageVars) {

         $thisModel = $this->getModelAndCheckDependencies(substr(get_class($this), 11), $pageVars) ;
         if (is_array($thisModel)) {
             return $this->failDependencies($pageVars, $this->content, $thisModel) ; }

         if ($thisModel->userIsAllowedAccess() !== true) {
             $override = $this->getIndexControllerForOverride() ;
             return $override->execute() ; }

         if ($pageVars["route"]["action"] === "image") {
             $this->content["params"]["output-format"] = 'image';
             $this->content["route"]["extraParams"]["output-format"] = 'image';
             $this->content["layout"] = 'blank';
             $this->content["data"] = $thisModel->getReleaseData() ;
             $this->content["route"] = $pageVars["route"] ;
             return array ("type"=>"view", "view"=>"publishReleasesList", "pageVars"=>$this->content) ; }

         if ($pageVars["route"]["action"] === "release") {
             $this->content["data"] = $thisModel->getReleasesData() ;
             return array ("type"=>"view", "view"=>"publishReleases", "pageVars"=>$this->content) ; }

         if ($pageVars["route"]["action"] === "releases-list") {
             $this->content["data"] = $thisModel->getReleasesList() ;
             return array ("type"=>"view", "view"=>"publishReleasesList", "pageVars"=>$this->content) ; }

         if ($pageVars["route"]["action"] === 'help') {
             $helpModel = new \Model\Help();
             $this->content["helpData"] = $helpModel->getHelpData($pageVars['route']['control']);
             return array ("type"=>"view", "view"=>"help", "pageVars"=>$this->content); }

         $this->content["messages"][] = "Invalid Publish Release Action";
         return array ("type"=>"control", "control"=>"index", "pageVars"=>$this->content);

     }

    protected function getIndexControllerForOverride() {
        return \Core\AutoLoader::getController("Signup")  ;
    }


}
