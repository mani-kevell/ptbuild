<?php

Namespace Info;

class LDAPInfo extends PTConfigureBase {

    public $hidden = false;

    public $name = "LDAP Integration";

    public function _construct() {
        parent::__construct();
    }

    public function routesAvailable() {
        return array("LDAP" => array("ldaplogin", "ldap-submit", "help") );
    }

    public function routeAliases() {
        return array("ldap"=>"LDAP");
    }

    public function configuration() {

        return array(
            "ldap_enabled" => array("type" => "boolean", "default" => "", "label" => "Enable Login by LDAP", ),
        );
    }


    public function helpDefinition() {
       $help = <<<"HELPDATA"
    This extension provides integration with LDAP as a Login Provider.

    LDAP

HELPDATA;
      return $help ;
    }

}
 
 
 
