<?php

Namespace Model;

class PharaohSourceIntegrationAllOS extends Base {

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
                "name" => "Enable Integrating with Pharaoh Source job/s?"
            ),
            "fieldsets" => array(
                "source_jobs" => array(
                    "url" =>
                        array(
                            "type" => "text",
                            "name" => "Source Job URL",
                            "slug" => "joburl"),
                    "title" =>
                        array(
                            "type" => "text",
                            "name" => "Job Title",
                            "slug" => "jobtitle"),
                )
            ),
        );
        return $ff ;
    }

    public function getStepTypes() {
        return array_keys($this->getFormFields());
    }

    public function getFormFields() {
        $source_servers = $this->getAvailableSourceServers() ;
        $ff = array(
            "create_repository" => array(
                array(
                    "type" => "dropdown",
                    "name" => "Server URL",
                    "data" => $source_servers,
                    "slug" => "server_url"),
                array(
                    "type" => "text",
                    "name" => "Repository Name",
                    "slug" => "repository_name"),
                ) ,
            "delete_repository" => array(
                "type" => "textarea",
                "name" => "Delete Source Code Repository",
                "slug" => "delete_repository" ) ,
        );
        return $ff ;
    }

    public function getAvailableSourceServers() {
        $settings = $this->getSettings() ;
        $servers = array() ;
        $source_max = 1;
        for ($i=0; $i<=$source_max; $i++) {
            if (isset($settings['PharaohSourceIntegration']['source_instance_url_'.$i])) {
                $servers[] = $settings['PharaohSourceIntegration']['source_instance_url_'.$i] ;
            }
        }
        return $servers;
    }

    protected function getSettings() {
        $settings = \Model\AppConfig::getAppVariable("mod_config");
        return $settings ;
    }

    public function executeStep($step) {
        $loggingFactory = new \Model\Logging();
        $logging = $loggingFactory->getModel($this->params);
        if ( $step["steptype"] == "create_repository") {
            $logging->log("Running Creation of a Repository in Pharaoh Source...", $this->getModuleName()) ;
            $logging->log("Calling API to {$step['server_url']}...", $this->getModuleName()) ;
            return true ; }
        else {
            $logging->log("Unrecognised Build Step Type {$step["type"]} specified in Shell Module", $this->getModuleName()) ;
            return false ; }
    }

    protected function createRepository($server, $repo_slug) {
        $settings = \Model\AppConfig::getAppVariable("mod_config");
        return $settings ;
    }



}