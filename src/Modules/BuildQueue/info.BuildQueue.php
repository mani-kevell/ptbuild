<?php

Namespace Info;

class BuildQueueInfo extends PTConfigureBase {

    public $hidden = false;

    public $name = "Functionality to provide queueing for your builds";

    public function _construct() {
        parent::__construct();
    }

    public function routesAvailable() {
        return array( "BuildQueue" => array_merge(parent::routesAvailable(), array("help", "findqueued", "run-cycle") ) );
    }

    public function routeAliases() {
        return array("buildqueue"=>"BuildQueue");
    }

    public function buildSettings() {
        return array("build_queue_enabled");
    }

    public function events() {
        return array("buildQueueEnable");
    } 
    
    public function helpDefinition() {
       $help = <<<"HELPDATA"
    This extension provides Functionality to provide runtime parameters to your build.
    It provides code functionality, but no extra commands.

    BuildQueue

HELPDATA;
      return $help ;
    }

}
