<?php

Namespace Model;

class PublishHTMLreportsAllOS extends Base {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("Default") ;

    public function getSettingTypes() {
        return array_keys($this->getSettingFormFields());
    }

    public function getSettingFormFields() {
        $ff = array(
            "enabled" =>
            	array(
                	"type" => "boolean",
                	"optional" => true,
                	"name" => "Publish HTML reports on Build Completion?"
            ),
            "fieldsets" => array(
                "reports" => array(
                    "Report_Directory" =>
                    array(
                        "type" => "text",
                        "name" => "HTML Directory to archive",
                        "slug" => "htmlreportdirectory"),
                    "Index_Page" =>
                    array("type" => "text",
                        "name" => "Index Page",
                        "slug" => "indexpage"),
                    "Report_Title" =>
                    array("type" => "text",
                        "name" => "Report Title",
                        "slug" => "reporttitle"),
                    "allow_public" =>
                    array("type" => "boolean",
                        "name" => "Allow Public Report Access?",
                        "slug" => "allow_public"))))
		    ;
          return $ff ;}
   
    public function getEventNames() {
        return array_keys($this->getEvents());   }

	public function getEvents() {
		$ff = array("afterBuildComplete" => array("PublishHTMLreports"));
		return $ff ;
    }

    public function getPipeline() {
        $pipelineFactory = new \Model\Pipeline() ;
        $pipeline = $pipelineFactory->getModel($this->params);
        $r = $pipeline->getPipeline($this->params["item"]);
        return $r ;
    }

    public function getReportListData() {
		$pipeFactory = new \Model\Pipeline();
		$pipeline = $pipeFactory->getModel($this->params);
		$thisPipe = $pipeline->getPipeline($this->params["item"]);
		$mn = $this->getModuleName() ;
		$ff = array(
            "report_list" => $thisPipe["settings"][$mn],
            "pipe" => $thisPipe
        );
//        var_dump("path", $root.$dir.$indexFile, "ff", $ff) ;
		return $ff ; }

	public function getReportData() {
		$pipeFactory = new \Model\Pipeline();
		$pipeline = $pipeFactory->getModel($this->params);
		$thisPipe = $pipeline->getPipeline($this->params["item"]);
		$settings = $thisPipe["settings"];
		$mn = $this->getModuleName() ;
		$dir = $settings[$mn]["reports"][$this->params["hash"]]["Report_Directory"] ;
        $dir = $this->ensureTrailingSlash($dir) ;
		$indexFile = $settings[$mn]["reports"][$this->params["hash"]]["Index_Page"];
        if (file_exists($dir.$indexFile) == true) { $root = "" ; }
		else { $root = PIPEDIR.DS.$this->params["item"].DS.'workspace'.DS ; }

        $report_file_path = $root.$dir.$indexFile ;

        if (file_exists($report_file_path))  {
            $report_data = file_get_contents($root.$dir.$indexFile) ; }
        else {
            $loggingFactory = new \Model\Logging();
            $this->params["echo-log"] = true ;
            $logging = $loggingFactory->getModel($this->params);
            $err = 'Unable to find a Report in the requested location' ;
            $logging->log($err, $this->getModuleName());
            $report_data = '<p>'.$err.'.</p>' ; }
        $ff = array(
            "current_report" => array(
                "hash" => $this->params["hash"] ,
                "feature_data" => $settings[$mn]["reports"][$this->params["hash"]],
                "report_data" => $report_data,
            )
        );
        $ff["pipeline"] = $this->getPipeline() ;
        $ff["current_user"] = $this->getCurrentUser() ;
        $ff["current_user_role"] = $this->getCurrentUserRole($ff["current_user"]);
        return $ff ;
    }

    protected function getCurrentUser() {
        $signupFactory = new \Model\Signup() ;
        $signup = $signupFactory->getModel($this->params);
        $user = $signup->getLoggedInUserData();
        return $user ;
    }

    public function getCurrentUserRole($user = null) {
        if ($user == null) { $user = $this->getCurrentUser(); }
        if ($user == false) { return false ; }
        return $user->role ;
    }

    public function isLoginEnabled() {
        $settings = $this->getSettings();
        if ( (isset($settings["Signup"]["signup_enabled"]) && $settings["Signup"]["signup_enabled"] !== "on")
            || !isset($settings["Signup"]["signup_enabled"])) {
            return false ; }
        return true ;
    }



    public function userIsAllowedAccess() {
        $user = $this->getCurrentUser() ;
        $pipeline = $this->getPipeline() ;
        $settings = $this->getSettings() ;
        if (!isset($settings["PublicScope"]["enable_public"]) ||
            ( isset($settings["PublicScope"]["enable_public"]) && $settings["PublicScope"]["enable_public"] != "on" )) {
            // if enable public is set to off
            if ($user == false) {
                // and the user is not logged in
                return false ; }
            // if they are logged in continue on
            return true ; }
        else {
            // if enable public is set to on
            if ($user == false) {
                // and the user is not logged in
                if ($pipeline["settings"]["PublicScope"]["enabled"] == "on" &&
                    $pipeline["settings"]["PublicScope"]["build_public_reports"] == "on") {
                    // if public pages are on
                    if ($pipeline["settings"]["PublishHTMLreports"]["reports"][$this->params["hash"]]["allow_public"]=="on") {
                        // if this report has public access enabled
                        return true ; }
                    else {
                        // if this report has public access disabled
                        return false ; } }
                else {
                    // if no public pages are on
                    return false ; } }
            else {
                // and the user is logged in
                // @todo this is where repo specific perms go when ready
                return true ;
            }
        }
    }

    protected function getSettings() {
        $settings = \Model\AppConfig::getAppVariable("mod_config");
        return $settings ;
    }

    public function PublishHTMLreports() {
        $loggingFactory = new \Model\Logging();
        $this->params["echo-log"] = true ;
        $logging = $loggingFactory->getModel($this->params);

        $file = PIPEDIR.DS.$this->params["item"].DS.'settings';
        $steps = file_get_contents($file) ;
        $steps = json_decode($steps, true);

        $mn = $this->getModuleName() ;
        if ($steps[$mn]["enabled"] == "on") {
            $dir = $steps[$mn]["Report_Directory"];
            if (substr($dir, -1) != DS) { $dir = $dir . DS ;}

            $indexFile = $steps[$mn]["Index_Page"];
            $ReportTitle = $steps[$mn]["Report_Title"];
            $tmpfile = PIPEDIR.DS.$this->params["item"].DS.'tmpfile';
            $raw = file_get_contents($tmpfile);
            if (!$raw) {
                $logging->log("This report has not been generated", $this->getModuleName(), LOG_FAILURE_EXIT_CODE);
                return false ; }
            else {
                $slug = "Report of Pipeline ".$this->params["item"]." for run-id ".$this->params["run-id"];
                $byline = "Pharaoh Build ";
                $html = nl2br(htmlspecialchars($raw));
                $html = str_replace("&lt;br /&gt;","<br />",$html);
                $html = preg_replace('/\s\s+/', ' ', $html);
                $html = preg_replace('/\s(\w+:\/\/)(\S+)/', ' <a href="\\1\\2" target="_blank">\\1\\2</a>', $html);

                $output =<<< HEADER
<html>
<head><title>"$ReportTitle"</title>
<style>
.slug {font-size: 15pt; font-weight: bold; font-style: italic}
.byline { font-style: italic }
</style>
</head>
<body>
HEADER;
                $output .= "<div class='slug'>$slug</div>";
                $output .= "<div class='byline'>By $byline</div><p />";
                $output .= "<div>$html</div>";
                $output .=<<< FOOTER
</body>
</html>
FOOTER;
                //save reference
                $reportRef = PIPEDIR.DS.$this->params["item"].DS.'HTMLreports'.DS;
                if (!file_exists($reportRef))
                {
                    mkdir($reportRef, 0777);
                }
                file_put_contents($reportRef.$indexFile . '-' . date("l jS \of F Y h:i:s A"), $output);

                //save Html report to given directory
                $source=$dir.$indexFile;
                if(file_put_contents($source,$output))
                {	return true;	}
                else	{ 	return false;	}
            }
        }
        else {
            $logging->log ("Unable to write generated report to file...", $this->getModuleName() ) ;
            return true ; }
    }


}