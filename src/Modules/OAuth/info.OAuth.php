<?php

Namespace Info;

class OAuthInfo extends PTConfigureBase {

	public $hidden = false;

	public $name = "login using OAuth Module";

	public function _construct() {
		parent::__construct();
	}

	public function routesAvailable() {
		return array("OAuth" => array("githublogin", "googlelogin", "fblogin", "linkedinlogin"));
	}

	public function routeAliases() {
		return array("oauth" => "OAuth");
	}

	public function configuration() {

		return array(
            "oauth_enabled" => array("type" => "boolean", "default" => "", "label" => "Enable Login by Oauth Providers", ),
            "google_enabled" => array("type" => "boolean", "default" => "", "label" => "Enable Login by Google Oauth Credentials", ),
            "google_client_id" => array("type" => "text", "default" => "client id", "label" => "Client ID", ),
            "google_client_secret" => array("type" => "text", "default" => "client secret", "label" => "Client Secret", ),
            "google_redirect_uri" => array("type" => "text", "default" => "http://www.example.com/index.php?control=OAuth&action=googlelogin", "label" => "Redirect URI", ),
            "github_enabled" => array("type" => "boolean", "default" => "", "label" => "Enable Login by Github Oauth Credentials", ),
            "github_client_id" => array("type" => "text", "default" => "client id", "label" => "Client ID", ),
            "github_client_secret" => array("type" => "text", "default" => "client secret", "label" => "Client Secret", ),
            "github_redirect_uri" => array("type" => "text", "default" => "http://www.example.com/index.php?control=OAuth&action=githublogin", "label" => "Redirect URI", ),
            "fb_enabled" => array("type" => "boolean", "default" => "", "label" => "Enable Login by Facebook Oauth Credentials", ),
            "fb_client_id" => array("type" => "text", "default" => "client id", "label" => "Client ID", ),
            "fb_client_secret" => array("type" => "text", "default" => "client secret", "label" => "Client Secret", ),
            "fb_redirect_uri" => array("type" => "text", "default" => "http://www.example.com/index.php?control=OAuth&action=fblogin", "label" => "Redirect URI", ),
            "li_enabled" => array("type" => "boolean", "default" => "", "label" => "Enable Login by Linked-In Oauth Credentials", ),
            "li_client_id" => array("type" => "text", "default" => "client id", "label" => "Client ID", ),
            "li_client_secret" => array("type" => "text", "default" => "client secret", "label" => "Client Secret", ),
            "li_redirect_uri" => array("type" => "text", "default" => "http://www.example.com/index.php?control=OAuth&action=linkedinlogin", "label" => "Redirect URI", ),
         );
	}

	public function helpDefinition() {
		$help = <<<"HELPDATA"
  This command is part of Core - its the default route and only used for help and as an Intro really...
HELPDATA;
		return $help;
	}

}
