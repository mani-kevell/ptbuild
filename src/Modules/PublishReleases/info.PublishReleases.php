<?php

Namespace Info;

class PublishStatusInfo extends PTConfigureBase {

    public $hidden = false;

    public $name = "Publish HTML statuses for build";

    public function _construct() {
        parent::__construct();
    }

    public function routesAvailable() {
        return array( "PublishStatus" => array_merge(parent::routesAvailable(), array("help", "image", "status", "status-list") ) );
    }

    public function routeAliases() {
        return array("publishhtmlstatuses"=>"PublishStatus","PublishStatus"=>"PublishStatus");
    }

    public function events() {
        return array("afterBuildComplete", "getBuildFeatures");
    }

    public function pipeFeatures() {
        return array("publishStatus");
    }

    public function buildSettings() {
        return array("publishStatus");
    }

    public function ignoredAuthenticationRoutes() {
        return array( "PublishStatus" => array("image", "status", "status-list") );
    }

    public function helpDefinition() {
       $help = <<<"HELPDATA"
    This extension publishes Current Status of a build. It provides code
    functionality, but no extra CLI commands.

    publishhtmlstatuses

HELPDATA;
      return $help ;
    }

}
