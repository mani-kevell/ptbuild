<?php

Namespace Info;

class PollSCMInfo extends PTConfigureBase {

    public $hidden = false;

    public $name = "PollSCM Provisioner Integration";

    public function _construct() {
        parent::__construct();
    }

    public function routesAvailable() {
        return array( "PollSCM" => array_merge(parent::routesAvailable(), array("help") ) );
    }

    public function routeAliases() {
        return array("sendemail"=>"PollSCM");
    }

    public function events() {
        return array("afterBuildComplete");
    }

    public function buildSettings() {
        return array("send_postbuild_email", "send_postbuild_email_stability", "send_postbuild_email_address");
    }

    public function configuration() {
        return array(
            "smtp_server"=> array( "type" => "text", "default" => "SMTP Server", "label" => "SMTP Server Address", ),
            "from_email"=> array( "type" => "text", "default" => "example@mail.com", "label" => "Email Address", ),
            "username"=> array( "type" => "text", "default" => "example", "label" => "Email Username", ),
            "password"=> array( "type" => "text", "default" => "none", "label" => "Email Password", ),
            "port"=> array( "type" => "text", "default" => "25", "label" => "Email Server Port", ),
        );
    }

    public function helpDefinition() {
       $help = <<<"HELPDATA"
    This extension provides integration with PollSCM as a Build Step. It provides code
    functionality, but no extra CLI commands.

    PollSCM

HELPDATA;
      return $help ;
    }

}