<?php

Namespace Info;

class PharaohAPIInfo extends PTConfigureBase {

    public $hidden = false;

    public $name = "Publish HTML reports for build";

    public function _construct() {
        parent::__construct();
    }

    public function routesAvailable() {
        return array( "PharaohAPI" => array_merge(parent::routesAvailable(), array("help", "report", "report-list") ) );
    }

    public function routeAliases() {
        return array("pharaohapi"=>"PharaohAPI","PharaohAPI"=>"PharaohAPI");
    }

    public function events() {
        return array("afterBuildComplete", "getBuildFeatures");
    }

    public function pipeFeatures() {
        return array("htmlReports");
    }

    public function buildSettings() {
        return array("htmlReports");
    }

    public function ignoredAuthenticationRoutes() {
        return array( "PharaohAPI" => array("report", "report-list") );
    }

    public function helpDefinition() {
       $help = <<<"HELPDATA"
    This extension publish HTML reports of a build. It provides code
    functionality, but no extra CLI commands.

    pharaohapi

HELPDATA;
      return $help ;
    }

}
