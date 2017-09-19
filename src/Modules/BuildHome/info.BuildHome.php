<?php

Namespace Info;

class BuildHomeInfo extends PTConfigureBase {

    public $hidden = false;

    public $name = "BuildHome Module";

    public function _construct() {
      parent::__construct();
    }

    public function routesAvailable() {
      return array( "BuildHome" => array("show", "delete") );
    }

    public function routeAliases() {
      return array("buildHome"=>"BuildHome");
    }

    public function ignoredAuthenticationRoutes() {
        return array( "BuildHome" => array("show") );
    }

    public function helpDefinition() {
      $help = <<<"HELPDATA"
  This is the Build Home page module for a single build...
HELPDATA;
      return $help ;
    }

}