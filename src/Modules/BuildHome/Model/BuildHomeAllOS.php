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
        $ret["settings"] = $this->getSettings();
        $ret["login_enabled"] = $this->isLoginEnabled();
        $ret["features"] = $this->getPipelineFeatures();
        $ret["historic_builds"] = $this->getOldBuilds();
        $ret["history_index"] = $this -> getHistoryIndex();
        $ret["current_user"] = $this->getCurrentUser() ;
        $ret["current_user_role"] = $this->getCurrentUserRole($ret["current_user"]);
        return $ret ;
    }

    protected function getCurrentUser() {
        $uaFactory = new \Model\UserAccount() ;
        $ua = $uaFactory->getModel($this->params);
        $user = $ua->getLoggedInUserData();
        return $user ;
    }

    public function getCurrentUserRole($user = null) {
        if ($user == null) { $user = $this->getCurrentUser(); }
        if ($user == false) { return false ; }
        return $user['role'] ;
    }

    public function isLoginEnabled() {
        $settings = $this->getSettings();
        if ( (isset($settings['Signup']['signup_enabled']) && $settings['Signup']['signup_enabled'] !== "on")
              || !isset($settings['Signup']['signup_enabled'])) {
            return false ; }
        return true ;
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

    public function getOldBuilds() {
        $builds = scandir(PIPEDIR.DS.$this->params["item"].DS.'history') ;
        $buildsRay = array();
        for ($i=0; $i<count($builds); $i++) {
            if (!in_array($builds[$i], array(".", "..", "tmpfile"))){
                $buildsRay[] = $builds[$i] ; } }
        rsort($buildsRay) ;
        return $buildsRay ;
    }

    public function getHistoryIndex() {
        $file = PIPEDIR . DS . $this -> params["item"] . DS . 'historyIndex';
        if ($historyIndex = file_get_contents($file)) {
            $historyIndex = json_decode($historyIndex, true);
        }
        return $historyIndex ;
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

    public function userIsAllowedAccess() {
        $user = $this->getCurrentUser() ;
        $pipeline = $this->getPipeline() ;
        $settings = $this->getSettings() ;
        if (!isset($settings['Signup']['signup_enabled']) ||
            ( isset($settings['Signup']['signup_enabled']) && $settings['Signup']['signup_enabled'] !== "on" )) {
            // if signups arent enabled neither are permissions
            return true ;
        }
        else if (!isset($settings["PublicScope"]["enable_public"]) ||
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
                if ($pipeline["settings"]["PublicScope"]["enabled"] == "on" &&
                    $pipeline["settings"]["PublicScope"]["build_public_home"] == "on") {
                    // if public pages are on
                    return true ; }
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