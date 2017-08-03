<?php

Namespace Info;

class PublishReleasesInfo extends PTConfigureBase {

    public $hidden = false;

    public $name = "Publish HTML statuses for build";

    public function _construct() {
        parent::__construct();
    }

    public function routesAvailable() {
        return array( "PublishReleases" => array_merge(parent::routesAvailable(), array("help", "image", "releases", "releases-list") ) );
    }

    public function routeAliases() {
        return array("publishhtmlstatuses"=>"PublishReleases","PublishReleases"=>"PublishReleases");
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
        return array( "PublishReleases" => array("image", "releases", "releases-list") );
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
