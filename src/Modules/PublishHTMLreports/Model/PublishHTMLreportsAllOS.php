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
        $last_run_id = $thisPipe['last_run_build'] ;
		$run_id = (isset($this->params["run-id"])) ? $this->params["run-id"] : $last_run_id ;

//		$dir = $settings[$mn]["reports"][$this->params["hash"]]["Report_Directory"] ;
//        $dir = $this->ensureTrailingSlash($dir) ;
		$indexFile = $settings[$mn]["reports"][$this->params["hash"]]["Index_Page"];

        $reportRef = PIPEDIR.DS.$this->params["item"].DS.'HTMLreports'.DS.$this->params["hash"].
            DS.$run_id.DS;

//        if (file_exists($dir.$indexFile) == true) {
//            $root = "" ; }
//		else {
//            $root = PIPEDIR.DS.$this->params["item"].DS.'workspace'.DS ; }

//        $report_file_path = $root.$dir.$indexFile ;
        $report_file_path = $reportRef.$indexFile ;


        if (file_exists($report_file_path))  {
            $report_data = file_get_contents($report_file_path) ; }
        else {
            $loggingFactory = new \Model\Logging();
//            $this->params["echo-log"] = true ;
            $logging = $loggingFactory->getModel($this->params);
            $err = 'Unable to find a Report in the requested location' ;
            $logging->log($err, $this->getModuleName());
            $report_data = '<p>'.$err.'.</p>' ; }
        $ff = array(
            "current_report" => array(
                "hash" => $this->params["hash"] ,
                "feature_data" => $settings[$mn]["reports"][$this->params["hash"]],
                "report_data" => $report_data,
                "requested_run_id" => $this->params["run-id"],
                "last_run_id" => $last_run_id,
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
//        $pipe_settings = PIPEDIR.DS.$this->params["item"].DS.'settings';
        $pipe = $this->getPipeline() ;
        $pipe_settings = $pipe['settings'];
        $mn = $this->getModuleName() ;
//        var_dump('my settings: ', $pipe_settings) ;
        if ($pipe_settings[$mn]["enabled"] == "on") {
            $logging->log("HTML Report publishing is enabled, executing", $this->getModuleName());
//            var_dump($pipe_settings[$mn]) ;
            foreach ($pipe_settings[$mn]['reports'] as $report_hash => $report_details) {
                $results = $this->publishOneReport($report_hash, $report_details) ;
            }
            return (in_array(false, $results)) ? true : false ;
        }
        else {
            $logging->log ("Unable to write generated report to file...", $this->getModuleName() ) ;
            return false ;
        }
    }

    protected function publishOneReport($one_report_hash, $one_report_details) {

        $loggingFactory = new \Model\Logging();
        $this->params["echo-log"] = true ;
        $logging = $loggingFactory->getModel($this->params);

        $dir = $one_report_details["Report_Directory"];
        $dir = $this->ensureTrailingSlash($dir) ;
        $dir = PIPEDIR.DS.$this->params["item"].DS.'workspace'.DS.$dir ;

        $ReportTitle = $one_report_details["Report_Title"];
        if (!is_dir($dir)) {
            $log_msg = "Unable to locate Report Directory {$dir} " ;
            $log_msg .= "from report {$ReportTitle}" ;
            $logging->log($log_msg, $this->getModuleName());
            return false ;
        }

        $indexFile = $one_report_details["Index_Page"];
        $source = $dir.$indexFile;
        $raw = file_get_contents($source);

        if (!$raw) {
            $logging->log("This report {$ReportTitle} has not been generated", $this->getModuleName());
            return false ; }
        else {

            $reportRef = PIPEDIR.DS.$this->params["item"].DS.'HTMLreports'.DS.$one_report_hash.DS.$this->params["run-id"].DS;
            $logging->log ("Publishing to report directory {$reportRef}", $this->getModuleName() ) ;
            if (!is_dir($reportRef))
            {
                $logging->log ("Attempting to create report directory {$reportRef}", $this->getModuleName() ) ;
                mkdir($reportRef, 0777, true);
            }
//                file_put_contents($reportRef.$indexFile . '-' . date("l jS \of F Y h:i:s A"), $output);

            //save Html report to given directory
//                if ( file_put_contents($source,$output) ) {
            if ( file_put_contents($reportRef.$indexFile, $raw) ) {
                $logging->log ("Report {$ReportTitle} published to file...", $this->getModuleName() ) ;
                return true; }
            else {
                $logging->log ("Unable {$ReportTitle} to publish generated report to file...", $this->getModuleName() ) ;
                return false;	}
        }
    }


}