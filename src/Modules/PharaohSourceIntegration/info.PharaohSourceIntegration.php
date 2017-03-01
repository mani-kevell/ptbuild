<?php

Namespace Info;

class PharaohSourceIntegrationInfo extends PTConfigureBase {

    public $hidden = false;

    public $name = "Provides Functionality to integrate Pharaoh Source Reports with a Repository";

    public function _construct() {
        parent::__construct();
    }

    public function routesAvailable() {
        return array( "PharaohSourceIntegration" => array_merge(parent::routesAvailable(), array("help") ) );
    }

    public function routeAliases() {
        return array("pharaohsourceintegration"=>"PharaohSourceIntegration");
    }

    public function repositorySettings() {
        return array();
    }

    public function events() {
        return array("getRepositoryFeatures");
    }

    public function repositoryFeatures() {
        return array("pharaohSourceIntegration");
    }

    public function configuration() {
        return array(
            "enabled"=> array(
                "type" => "boolean",
                "default" => "",
                "label" => "Enable Pharaoh Source Integration?", ),
            "source_instance_url_0"=> array(
                "type" => "text",
                "default" => "",
                "label" => "Home page of Source Instance 0?", ),
            "source_instance_key_0"=> array(
                "type" => "text",
                "default" => "",
                "label" => "API Key of Source Instance 0?", ),
            "source_instance_url_1"=> array(
                "type" => "text",
                "default" => "",
                "label" => "Home page of Source Instance 1?", ),
            "source_instance_key_1"=> array(
                "type" => "text",
                "default" => "",
                "label" => "API Key of Source Instance 1?", ),
        );
    }

    public function helpDefinition() {
       $help = <<<"HELPDATA"
    This extension provides integration with Pharaoh Source Reports for a Repository ,
    but no extra CLI commands.

    PharaohSourceIntegration

HELPDATA;
      return $help ;
    }

}