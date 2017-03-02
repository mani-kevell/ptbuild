<?php

Namespace Info;

class PublishStatusInfo extends PTConfigureBase {

    public $hidden = false;

    public $name = "Publish HTML reports for build";

    public function _construct() {
        parent::__construct();
    }

    public function routesAvailable() {
        return array( "PublishStatus" => array_merge(parent::routesAvailable(), array("help", "report", "report-list") ) );
    }

    public function routeAliases() {
        return array("publishhtmlreports"=>"PublishStatus","PublishStatus"=>"PublishStatus");
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
        return array( "PublishStatus" => array("report", "report-list") );
    }

    public function helpDefinition() {
       $help = <<<"HELPDATA"
    This extension publish HTML reports of a build. It provides code
    functionality, but no extra CLI commands.

    publishhtmlreports

HELPDATA;
      return $help ;
    }

}
