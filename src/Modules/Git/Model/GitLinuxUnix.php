<?php

Namespace Model;

class GitLinuxUnix extends Base {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("Default") ;

    public function getStepTypes() {
        return array_keys($this->getFormFields());
    }

    public function getFormFields() {
        $ff = array(
            "gitclonepoll" => array(
                "type" => "boolean",
                "name" => "Git clone using Polling repo",
                "slug" => "pollrepo" ),
            "gitclonedir" => array(
                "type" => "text",
                "name" => "Clone Directory",
                "slug" => "clonedir" ),

            "git_clone_custom" => array(
                array(
                    "type" => "text",
                    "optional" => true,
                    "name" => "Git Repository URL?",
                    "slug" => "git_repo_url"
                ),
                array(
                    "type" => "text",
                    "optional" => true,
                    "name" => "Git Branch?",
                    "slug" => "git_repo_branch"
                ),
                array(
                    "type" => "text",
                    "optional" => true,
                    "name" => "Target Directory?",
                    "slug" => "git_repo_target_dir"
                ),
                array(
                    "type" => "text",
                    "optional" => true,
                    "name" => "Git Private Key Path?",
                    "slug" => "git_repo_private_key"
                ),
            ) ,
        );
        return $ff ;
    }

    public function executeStep($step, $item, $hash) {
        $loggingFactory = new \Model\Logging();
        $logging = $loggingFactory->getModel($this->params);

        if ( $step["steptype"] == "gitclonepoll") {

            $mod_fields = array('git_repo_url', 'git_branch', 'git_repo_target_dir', 'git_repo_private_key') ;

            if (isset($this->params["env-vars"]) && is_array($this->params["env-vars"])) {
                $logging->log("Git Integration Extracting Environment Variables...", $this->getModuleName()) ;
                foreach ($this->params["env-vars"] as $env_var_key => $env_var_val) {
                    foreach ($mod_fields as $field) {
                        if (strpos($step[$field], '$'.$env_var_key) !== false) {
                            $logging->log('Found Variable $'.$env_var_key.', replacing', $this->getModuleName()) ;
                            $step[$field] = str_replace('$'.$env_var_key, $env_var_val, $step[$field] ) ; } } } }
            $repo = $this->params["build-settings"]["PollSCM"]["git_repository_url"] ;

            $targetDir = (isset($this->params["build-settings"]["PollSCM"]["target_dir"]))
                ? $this->params["build-settings"]["PollSCM"]["target_dir"]
                : getcwd() ;
            $logging->log("Running Git clone from default repo $repo to ".$targetDir."...", $this->getModuleName()) ;

            $cmd = PTDCOMM.'GitClone clone --yes --guess --change-owner-permissions="false" '.
                ' --repository-url="'.$repo.'"' ;

            if (strlen($targetDir > 0)) {
                $cmd .= ' --custom-clone-dir="'.$targetDir.'" ' ; }

            if (isset($this->params["build-settings"]["PollSCM"]["git_privkey_path"]) &&
                $this->params["build-settings"]["PollSCM"]["git_privkey_path"] != "")  {
                $logging->log("Adding Private Key for cloning Git", $this->getModuleName()) ;
                $cmd .= ' --private-key="'.$this->params["build-settings"]["PollSCM"]["git_privkey_path"].'" ' ; }

            if (isset($this->params["build-settings"]["PollSCM"]["git_branch"]) &&
                $this->params["build-settings"]["PollSCM"]["git_branch"] != "")  {
                $logging->log("Adding Custom Branch for cloning Git", $this->getModuleName()) ;
                $cmd .= ' --custom-branch="'.$this->params["build-settings"]["PollSCM"]["git_branch"].'" ' ; }

            echo $cmd."\n" ;

            $rc = self::executeAndGetReturnCode($cmd, true, true) ;
            return ($rc["rc"]==0) ? true : false; }
        else if ( $step["steptype"] == "git_clone_custom") {

            $mod_fields = array('git_repo_url', 'git_branch', 'git_repo_target_dir', 'git_repo_private_key') ;

            if (isset($this->params["env-vars"]) && is_array($this->params["env-vars"])) {
                $logging->log("Git Integration Extracting Environment Variables...", $this->getModuleName()) ;
                foreach ($this->params["env-vars"] as $env_var_key => $env_var_val) {
                    foreach ($mod_fields as $field) {
                        if (strpos($step[$field], '$'.$env_var_key) !== false) {
                            $logging->log('Found Variable $'.$env_var_key.', replacing', $this->getModuleName()) ;
                            $step[$field] = str_replace('$'.$env_var_key, $env_var_val, $step[$field] ) ; } } } }

            $repo = $this->params["build-settings"]["PollSCM"]["git_repository_url"] ;

            $targetDir = (isset($step["git_repo_target_dir"]))
                ? $step["git_repo_target_dir"]
                : getcwd() ;

            $logging->log("Running Git clone from default repo $repo to ".$targetDir."...", $this->getModuleName()) ;

            $cmd = PTDCOMM.'GitClone clone --yes --guess --change-owner-permissions="false" '.
                ' --repository-url="'.$repo.'"' ;

            if (strlen($targetDir > 0)) {
                $cmd .= ' --custom-clone-dir="'.$targetDir.'" ' ; }

            if (isset($step["git_repo_private_key"]) &&
                $step["git_repo_private_key"] != "")  {
                $logging->log("Adding Private Key for cloning Git", $this->getModuleName()) ;
                $cmd .= ' --private-key="'.$step["git_repo_private_key"].'" ' ; }

            if (isset($step["git_branch"]) &&
                $step["git_branch"] != "")  {
                $logging->log("Adding Custom Branch for cloning Git", $this->getModuleName()) ;
                $cmd .= ' --custom-branch="'.$step["git_branch"].'" ' ; }

            echo $cmd."\n" ;

            $rc = self::executeAndGetReturnCode($cmd, true, true) ;

            $status = ($rc["rc"]==0) ? true : false;
            $status_ray = array() ;
            $status_ray['status'] = $status ;
            $status_ray['meta'][$this->getModuleName()][$hash] = $step ;

            return $status_ray ;
        }
        else {
            $logging->log("Unrecognised Build Step Type {$step["type"]} specified in Git Module", $this->getModuleName()) ;
            return false ; }
    }

}
