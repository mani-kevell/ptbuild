<?php

Namespace Info;

class PrepareStepsInfo extends PTConfigureBase {

    public $hidden = false;

    public $name = "Prepare steps for your build";

    public function _construct() {
        parent::__construct();
    }

    public function routesAvailable() {
        return array( "PrepareSteps" => array_merge(parent::routesAvailable(), array("help") ) );
    }

    public function routeAliases() {
        return array("preparesteps"=>"PrepareSteps");
    }

    public function events() {
        return array("beforeBuild");
    } 
    
    public function helpDefinition() {
       $help = <<<"HELPDATA"
    This extension prepares steps for a Build Step. It provides code
    functionality, but no extra commands.

    PrepareSteps

HELPDATA;
      return $help ;
    }

}
