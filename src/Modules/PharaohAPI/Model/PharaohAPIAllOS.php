<?php

Namespace Model;

class PharaohAPIAllOS extends Base {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("Default") ;

    public function getSettingTypes() {
        return array_keys($this->getSettingFormFields());
    }

    public function getSettingFormFields() {
        $ff = array(
            "enabled" =>
            	array(
                	"type" => "boolean",
                	"optional" => true,
                	"name" => "Publish HTML reports on Build Completion?"
            ),
            "fieldsets" => array(
                "reports" => array(
                    "Report_Directory" =>
                    array(
                        "type" => "text",
                        "name" => "HTML Directory to archive",
                        "slug" => "htmlreportdirectory"),
                    "Index_Page" =>
                    array("type" => "text",
                        "name" => "Index Page",
                        "slug" => "indexpage"),
                    "Report_Title" =>
                    array("type" => "text",
                        "name" => "Report Title",
                        "slug" => "reporttitle"),
                    "allow_public" =>
                    array("type" => "boolean",
                        "name" => "Allow Public Report Access?",
                        "slug" => "allow_public")))
            )
		    ;
          return $ff ;}
   
//    public function getEventNames() {
//        return array_keys($this->getEvents());   }
//
//	public function getEvents() {
//		$ff = array("afterBuildComplete" => array("PharaohAPI"));
//		return $ff ;
//    }

//    public function getPipeline() {
//        $pipelineFactory = new \Model\Pipeline() ;
//        $pipeline = $pipelineFactory->getModel($this->params);
//        $r = $pipeline->getPipeline($this->params["item"]);
//        return $r ;
//    }
//
//    protected function getCurrentUser() {
//        $signupFactory = new \Model\Signup() ;
//        $signup = $signupFactory->getModel($this->params);
//        $user = $signup->getLoggedInUserData();
//        return $user ;
//    }

    protected function getCurrentKey() {

        $settings = $this->getSettings() ;

        if ($settings['PharaohAPI']['enabled'] === 'on') {
            for ($i=0; $i<5; $i++) {
                'api_key_'.$i ;
            }


        }
        else {
            // API is not enabled
            return false ;
        }
        $signupFactory = new \Model\Signup() ;
        $signup = $signupFactory->getModel($this->params);
        $user = $signup->getLoggedInUserData();
        return $user ;
    }

    protected function isAPIEnabled() {
        $settings = $this->getSettings() ;
        if ($settings['PharaohAPI']['enabled'] === 'on') {
            return true ; }
        else {
            return false ; }
    }

    public function keyIsAllowedAccess() {
        $key_exists = $this->getCurrentKey() ;
        $pipeline = $this->getPipeline() ;
        $api_enabled = $this->isAPIEnabled() ;

        if ($api_enabled  !== true) {
            return false ;
        }

        if ($key_exists === false) {
            return false ;
        }



    }

    protected function getSettings() {
        $settings = \Model\AppConfig::getAppVariable("mod_config");
        return $settings ;
    }


}