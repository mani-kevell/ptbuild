<?php

Namespace Model;

class PublishStatusAllOS extends Base {

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
                	"name" => "Publish Status on Build Completion?"
            ),
            "allow_public" =>
                array(
                    "type" => "boolean",
                    "name" => "Allow Public Status Access?",
                    "slug" => "allow_public"
            ),
            "publish_image" =>
                array(
                    "type" => "boolean",
                    "name" => "Publish Image?",
                    "slug" => "publish_image"
            ),
            "publish_json" =>
                array(
                    "type" => "boolean",
                    "name" => "Publish JSON?",
                    "slug" => "publish_json"
            ),
            "publish_html" =>
                array(
                    "type" => "boolean",
                    "name" => "Publish HTML?",
                    "slug" => "publish_html"
            ),
//            "fieldsets" => array(
//                "status_image" => array(
//                    "enable_image" =>
//                        array("type" => "boolean",
//                            "name" => "Enable Status Image?",
//                            "slug" => "enable_image")),
//                "status_json" => array(
//                    "enable_json" =>
//                        array("type" => "boolean",
//                            "name" => "Enable Status JSON?",
//                            "slug" => "enable_json")))
        ) ;
        return $ff ;
    }
   
    public function getEventNames() {
        return array_keys($this->getEvents());
    }

	public function getEvents() {
		$ff = array("afterBuildComplete" => array("PublishStatus"));
		return $ff ;
    }

    public function getPipeline() {
        $pipelineFactory = new \Model\Pipeline() ;
        $pipeline = $pipelineFactory->getModel($this->params);
        $r = $pipeline->getPipeline($this->params["item"]);
        return $r ;
    }

    public function getStatusListData() {
		$pipeFactory = new \Model\Pipeline();
		$pipeline = $pipeFactory->getModel($this->params);
		$thisPipe = $pipeline->getPipeline($this->params["item"]);
		$mn = $this->getModuleName() ;
		$ff = array(
            "status_list" => $thisPipe["settings"][$mn],
            "pipe" => $thisPipe
        );
//        var_dump("path", $root.$dir.$indexFile, "ff", $ff) ;
		return $ff ; }

	public function getStatusData() {
        $ff["is_https"] = $this->isSecure();
        $ff["pipeline"] = $this->getPipeline() ;
        $ff["current_user"] = $this->getCurrentUser() ;
        $ff["current_user_role"] = $this->getCurrentUserRole($ff["current_user"]);
        return $ff ;
    }

    protected function isSecure() {
        return
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || $_SERVER['SERVER_PORT'] == 443;
    }

    protected function getCurrentUser() {
        $signupFactory = new \Model\Signup() ;
        $signup = $signupFactory->getModel($this->params);
        $user = $signup->getLoggedInUserData();
        return $user ;
    }

    public function getCurrentUserRole($user = null) {
        if ($user == null) { $user = $this->getCurrentUser(); }
        if ($user == false) { return false ; }
        return $user['role'] ;
    }

    public function isLoginEnabled() {
        $settings = $this->getSettings();
        if ( (isset($settings["Signup"]["signup_enabled"]) && $settings["Signup"]["signup_enabled"] !== "on")
            || !isset($settings["Signup"]["signup_enabled"])) {
            return false ; }
        return true ;
    }



    public function userIsAllowedAccess() {
        $user = $this->getCurrentUser() ;
        $pipeline = $this->getPipeline() ;
        $settings = $this->getSettings() ;
        if (!isset($settings["PublicScope"]["enable_public"]) ||
            ( isset($settings["PublicScope"]["enable_public"]) && $settings["PublicScope"]["enable_public"] != "on" )) {
            // if enable public is set to off
            if ($user == false) {
                // and the user is not logged in
                return false ; }
            // if they are logged in continue on
            return true ; }
        else {
            // if enable public is set to on
            if ($user == false) {
                // and the user is not logged in
                if ($pipeline["settings"]["PublicScope"]["enabled"] == "on" &&
                    $pipeline["settings"]["PublicScope"]["build_public_statuses"] == "on") {
                    // if public pages are on
                    if ($pipeline["settings"]["PublishStatus"]["statuses"][$this->params["hash"]]["allow_public"]=="on") {
                        // if this status has public access enabled
                        return true ; }
                    else {
                        // if this status has public access disabled
                        return false ; } }
                else {
                    // if no public pages are on
                    return false ; } }
            else {
                // and the user is logged in
                // @todo this is where repo specific perms go when ready
                return true ;
            }
        }
    }

    protected function getSettings() {
        $settings = \Model\AppConfig::getAppVariable("mod_config");
        return $settings ;
    }


}