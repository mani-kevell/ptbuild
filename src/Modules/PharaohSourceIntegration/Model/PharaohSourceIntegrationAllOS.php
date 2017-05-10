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
                $s = $settings['PharaohSourceIntegration']['source_instance_url_'.$i] ;
                $servers[$s] = $s ;
            }
        }
        return $servers;
    }

    protected function getSettings() {
        $settings = \Model\AppConfig::getAppVariable("mod_config");
        return $settings ;
    }

    public function executeStep($step, $item, $hash) {
        $loggingFactory = new \Model\Logging();
        $logging = $loggingFactory->getModel($this->params);
        if ( $step["steptype"] == "create_repository") {
            $logging->log("Running Creation of a Repository in Pharaoh Source...", $this->getModuleName()) ;

            $pf = new \Model\Pipeline() ;
            $pipelineBase = $pf->getModel($this->params) ;
            $pipelineInstance = $pipelineBase->getPipeline($item) ;

            $server_url = $pipelineInstance['steps'][$hash]["server_url"] ;
            $logging->log("Calling API to {$server_url}...", $this->getModuleName()) ;
            $res = $this->createRepository($server_url, $pipelineInstance['steps'][$hash]["repository_name"]) ;

            return $res ; }
        else {
            $logging->log("Unrecognised Build Step Type {$step["type"]} specified in Shell Module", $this->getModuleName()) ;
            return false ; }
    }

    protected function createRepository($server_url, $repo_name) {
        // load repo
        $this->params['item'] = $this->params['slug'] ;

        $apif = new \Model\PharaohAPI();
        $params = $this->params ;
        $params['api_module'] = 'RepositoryConfigure' ;
        $params['api_function'] = 'create_repository' ;
        $params['api_instance_url'] = $server_url ;
        $params['api_key'] = $this->findInstanceKey($server_url) ;
        $params['api_param_repo_name'] = $repo_name ;

        $api_request = $apif->getModel($params, 'Request') ;
        $result = $api_request->performAPIRequest() ;

        return $result;

    }


    public function findInstanceKey($instance_url) {
        $instance_url = $this->ensureTrailingSlash($instance_url) ;
        $settings = $this->getSettings() ;
        $instance_key = false ;
        if ($settings['PharaohBuildIntegration']['enabled'] === 'on') {
            for ($i=0; $i<5; $i++) {
                if (isset($settings['PharaohBuildIntegration']['build_instance_url_'.$i])) {
                    $url = $settings['PharaohBuildIntegration']['build_instance_url_'.$i] ;
                    $url_with_slash = $this->ensureTrailingSlash($url) ;
                    if ($url_with_slash === $instance_url) {
                        $instance_key = $settings['PharaohBuildIntegration']['build_instance_key_'.$i] ;
                    }
                }
            }
        }
        else {
            // Build Integration is not enabled
            return false ;
        }
        return $instance_key ;

    }

}