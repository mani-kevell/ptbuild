<?php

Namespace Info;

class PharaohTrackIntegrationInfo extends PTConfigureBase {

    public $hidden = false;

    public $name = "Provides Functionality to integrate Pharaoh Track Reports with a Repository";

    public function _construct() {
        parent::__construct();
    }

    public function routesAvailable() {
        return array( "PharaohTrackIntegration" => array_merge(parent::routesAvailable(), array("help") ) );
    }

    public function routeAliases() {
        return array("pharaohtrackintegration"=>"PharaohTrackIntegration");
    }

    public function repositorySettings() {
        return array();
    }

    public function events() {
        return array("getRepositoryFeatures");
    }

    public function repositoryFeatures() {
        return array("pharaohTrackIntegration");
    }

    public function configuration() {
        return array(
            "enabled"=> array(
                "type" => "boolean",
                "default" => "",
                "label" => "Enable Pharaoh Track Integration?", ),
            "track_instance_url_0"=> array(
                "type" => "text",
                "default" => "",
                "label" => "Home page of Track Instance 0?", ),
            "track_instance_key_0"=> array(
                "type" => "text",
                "default" => "",
                "label" => "API Key of Track Instance 0?", ),
            "track_instance_url_1"=> array(
                "type" => "text",
                "default" => "",
                "label" => "Home page of Track Instance 1?", ),
            "track_instance_key_1"=> array(
                "type" => "text",
                "default" => "",
                "label" => "API Key of Track Instance 1?", ),
        );
    }

    public function helpDefinition() {
       $help = <<<"HELPDATA"
    This extension provides integration with Pharaoh Track Reports for a Repository ,
    but no extra CLI commands.

    PharaohTrackIntegration

HELPDATA;
      return $help ;
    }

}