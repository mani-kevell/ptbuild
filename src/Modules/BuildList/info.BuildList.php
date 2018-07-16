<?php

Namespace Info;

class BuildListInfo extends PTConfigureBase {

    public $hidden = false;

    public $name = "BuildList Module";

    public function _construct() {
      parent::__construct();
    }

    public function routesAvailable() {
      return array( "BuildList" => array("show", 'buildstatus') );
    }

    public function routeAliases() {
      return array("buildList"=>"BuildList");
    }

    public function configuration() {
        return array(
            "index_override"=> array(
                "type" => "boolean",
                "default" => true,
                "label" => "Override your default Index Module with Build Listing as a Home Page?",
            ),
        );
    }

    public function helpDefinition() {
      $help = <<<"HELPDATA"
  This command is part of Core - its the default route and only used for help and as an Intro really...
HELPDATA;
      return $help ;
    }

}