<?php

Namespace Model;

class PublishReleasesAllOS extends Base {

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
                	"name" => "Publish Releases on Build Completion?"
            ),
            "allow_public" =>
                array(
                    "type" => "boolean",
                    "name" => "Allow Public Release Access?",
                    "slug" => "allow_public"
            ),
            "fieldsets" => array(
                "custom_release" => array(
                    "release_title" =>
                        array(
                            "type" => "text",
                            "name" => "Release Title",
                            "slug" => "releasetitle"),
                    "release_file" =>
                        array(
                            "type" => "text",
                            "name" => "Relative or absolute path to file",
                            "slug" => "release_file"),
                    "image" =>
                        array(
                            "type" => "text",
                            "name" => "Image for Release Package",
                            "slug" => "image"),
                    "allow_public" =>
                        array(
                            "type" => "boolean",
                            "name" => "Allow Public Report Access?",
                            "slug" => "allow_public")
                ),
            ),
        ) ;
        return $ff ;
    }
   
    public function getEventNames() {
        return array_keys($this->getEvents());
    }

	public function getEvents() {
		$ff = array("afterBuildComplete" => array("publishReleases"));
		return $ff ;
    }

    public function getPipeline() {
        $pipelineFactory = new \Model\Pipeline() ;
        $pipeline = $pipelineFactory->getModel($this->params);
        $r = $pipeline->getPipeline($this->params["item"]);
        return $r ;
    }

    public function getReleasesListData() {
		$pipeFactory = new \Model\Pipeline();
		$pipeline = $pipeFactory->getModel($this->params);
		$thisPipe = $pipeline->getPipeline($this->params["item"]);
		$mn = $this->getModuleName() ;
		$ff = array(
            "status_list" => $thisPipe["settings"][$mn],
            "pipe" => $thisPipe
        );
		return $ff ;
    }

	public function getReleasesData() {
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
        if ($user === null) {
            $user = $this->getCurrentUser(); }
        if ($user === false) {
            return false ; }
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
            ( isset($settings["PublicScope"]["enable_public"]) && $settings["PublicScope"]["enable_public"] !== "on" )) {
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
                if ($pipeline["settings"]["PublicScope"]["enabled"] === "on" &&
                    $pipeline["settings"]["PublicScope"]["build_public_statuses"] === "on") {
                    // if public pages are on
                    if ($pipeline["settings"]["PublishReleases"]["statuses"][$this->params["hash"]]["allow_public"] === "on") {
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

    public function publishReleases() {
        $loggingFactory = new \Model\Logging();
        $this->params["echo-log"] = true ;
        $logging = $loggingFactory->getModel($this->params);
        $pipe = $this->getPipeline() ;
        $pipe_settings = $pipe['settings'];
        $mn = $this->getModuleName() ;
        if ($pipe_settings[$mn]["enabled"] === "on") {
            $logging->log("Release publishing is enabled, executing", $this->getModuleName());
            $results = array() ;
            foreach ($pipe_settings[$mn]['reports'] as $report_hash => $report_details) {
                $results = $this->publishOneRelease($report_hash, $report_details) ;
            }
            return (in_array(false, $results)) ? true : false ;
        }
        else {
            $logging->log ("Release Publishing disabled...", $this->getModuleName() ) ;
            return false ;
        }
    }

}