<?php

Namespace Model;

class ScheduledBuildAllOS extends Base {

	// Compatibility
	public $os = array("any") ;
	public $linuxType = array("any") ;
	public $distros = array("any") ;
	public $versions = array("any") ;
	public $architectures = array("any") ;

	// Model Group
	public $modelGroup = array("Default");
    private $lm ;
    private $pipeline ;

    public function getSettingTypes() {
        return array_keys($this->getSettingFormFields());
    }

    public function getSettingFormFields() {
        $ff = array(
            "enabled" =>
            array(
                "type" => "boolean",
                "optional" => true,
                "name" => "Enable running builds via schedule?"
            ),
            "cron_string" =>
            array(
                "type" => "textarea",
                "optional" => true,
                "name" => "Crontab Values"
            ),
        );
        return $ff ;
    }

    public function getEventNames() {
        return array_keys($this->getEvents());
    }

    public function getEvents() {
        $ff = array(
            "prepareBuild" => array(
                "checkBuildSchedule",
            ),
        );
        return $ff ;
    }

    public function checkBuildSchedule() {
        $loggingFactory = new \Model\Logging();
        if (!$this->isWebSapi()) { $this->params["echo-log"] = true ; }
        $this->params["php-log"] = true ;
        $this->params["app-log"] = true ;
        $this->pipeline = $this->getPipeline($this->params["item"]);
        $this->params["build-settings"] = $this->pipeline["settings"];
        $this->params["app-settings"]["app_config"] = \Model\AppConfig::getAppVariable("app_config");
        $this->params["app-settings"]["mod_config"] = \Model\AppConfig::getAppVariable("mod_config");
        $this->lm = $loggingFactory->getModel($this->params);
        if ($this->checkBuildScheduleEnabledForBuild()) {
            $this->lm->log ("BSE", $this->getModuleName() ) ;

            return $this->doBuildScheduleEnabled() ; }
        else {
            $this->lm->log ("BSD", $this->getModuleName() ) ;

            return $this->doBuildScheduleDisabled() ; }
    }

    private function checkBuildScheduleEnabledForBuild() {
        $mn = $this->getModuleName() ;
        return ($this->params["build-settings"][$mn]["enabled"] == "on") ? true : false ;
    }

    private function doBuildScheduleDisabled() {
        $this->lm->log ("Time Scheduled Builds Disabled, ignoring...", $this->getModuleName() ) ;
        return true ;
    }

    private function doBuildScheduleEnabled() {
        $mn = $this->getModuleName() ;
        $this->lm->log ("Time Scheduled Builds Enabled for {$this->pipeline["project-name"]}, attempting...", $this->getModuleName() ) ;
        try {
            // @todo other scm types @kevellcorp do svn
            $lastSha = (isset($this->params["build-settings"][$mn]["last_sha"])) ? $this->params["build-settings"][$mn]["last_sha"] : null ;
            if (strlen($lastSha)>0) { $result = $this->doLastCommitStored() ; }
            else { $result = $this->doNoLastCommitStored() ; }
            return $result; }
        catch (\Exception $e) {
            $this->lm->log ("Error polling scm", $this->getModuleName() ) ;
            return false; }
    }

    public function getData() {
        $ret["scheduled"] = $this->getPipelinesRequiringExecution();
        $ret["executions"] = $this->executePipes($ret["scheduled"]);
        return $ret ;
    }

    private function executePipes($pipes) {
        $prFactory = new \Model\PipeRunner() ;
        $results = array();
        foreach ($pipes as $pipe) {
            $params = $this->params ;
            $params["item"] = $pipe["project-slug"] ;
            $params["build-request-source"] = "schedule" ;
            $settings = $pipe["settings"];
            $settings["ScheduledBuild"]["last_scheduled"] = time() ;
            $pipelineFactory = new \Model\Pipeline() ;
            $pipelineSaver = $pipelineFactory->getModel($params, "PipelineSaver");
            // @todo dunno why i have to force this param
            $pipelineSaver->params["item"] = $params["item"];
            $pipelineSaver->savePipeline(array("type" => "Settings", "data" => $settings ));
            $pr = $prFactory->getModel($params) ;
            $results[$pipe["project-slug"]] =
                array(
                    "name" => $pipe["project-name"],
                    "result" => $pr->runPipe()
                ) ; }
        return $results ;
    }

    public function getPipelinesRequiringExecution() {
        $psts = $this->getPipelinesWithScheduledTasks() ;
        $psrxs = array() ;

        $loggingFactory = new \Model\Logging();
        $params["app-log"] = true ;
        $logging = $loggingFactory->getModel($params);

        for ($i = 0; $i<count($psts) ; $i++) {
            $prx = $this->pipeRequiresExecution($psts[$i][1], $psts[$i][0]) ;
            if ($prx == true) {
                $psrxs[] = $psts[$i][1] ; } }
        return $psrxs;
    }

    // @todo we need to check multiple modules and return true if any are true, we should also
    // @todo say which one of the mods is tru
    public function pipeRequiresExecution($pst, $mod="ScheduledBuild") {
        $prx = array();
        $loggingFactory = new \Model\Logging();
        $params["app-log"] = true ;
        $logging = $loggingFactory->getModel($params);

        if ( $mod=="ScheduledBuild" &&
             isset($pst["settings"][$mod]["enabled"]) &&
             $pst["settings"][$mod]["enabled"] == "on" ) {
            $logging->log("Checking if Pipeline '{$pst["project-name"]}' Requires Execution by schedule") ;
            $cronString = $pst["settings"][$mod]["cron_string"] ;
            $cronString = rtrim($cronString) ;
            $cronString = ltrim($cronString) ;
            $lastRun = (isset($pst["settings"][$mod]["last_scheduled"]))
                ? $pst["settings"][$mod]["last_scheduled"]
                : 0 ;
            $res = $this->slotShouldRun($cronString, $lastRun) ;
            if ($res == true) {
                $logging->log("Pipeline '{$pst["project-name"]}' does require Execution by schedule now") ; }
            else {
                $logging->log("Pipeline '{$pst["project-name"]}' does not require Execution by schedule now") ; }
            return $res ; }
        else if ($mod=="PollSCM" &&
            isset($pst["settings"][$mod]["enabled"]) &&
            $pst["settings"][$mod]["enabled"] == "on") {
            $logging->log("Checking if Pipeline '{$pst["project-name"]}' requires Polling SCM by schedule now") ;
            $cronString = $pst["settings"][$mod]["cron_string"] ;
            $cronString = rtrim($cronString) ;
            $cronString = ltrim($cronString) ;
//            ob_start();
//            var_dump($pst["settings"][$mod]) ;
//            $pst_string = ob_get_clean() ;
//            $logging->log("this mod, $pst_string") ;
            $lastRun = (isset($pst["settings"][$mod]["last_poll_timestamp"]))
                ? $pst["settings"][$mod]["last_poll_timestamp"]
                : 0 ;
            $res = $this->slotShouldRun($cronString, $lastRun) ;
//            $logging->log("this mod 2, $res") ;
            if ($res == true) {
                $logging->log("Pipeline '{$pst["project-name"]}' does require Polling SCM by schedule now") ; }
            else {
                $logging->log("Pipeline '{$pst["project-name"]}' does not require Polling SCM by schedule now") ; }
            return $res ; }

        return false ;
    }

    private function slotShouldRun($value, $lastRun) {
        $loggingFactory = new \Model\Logging();
        $params["app-log"] = true ;
        $logging = $loggingFactory->getModel($params);
        $this->libLoader() ;
        $cron = \Cron\CronExpression::factory($value);
        $isValid = $cron::isValidExpression($value);
        if ($isValid !== true) {
            $logging->log("Invalid Cron Value Specified: {$value}") ;
            return false ; }
//      @todo the below log calls as log level of verbose
        $lastRunDate = new \DateTime() ;
//        $logging->log("lastRunDate DateTime created") ;
        $lastRunDate->setTimestamp($lastRun) ;
//        $logging->log("lastRunDate object timestamp of {$lastRun} has been set") ;
        $realNextRun = $cron->getNextRunDate($lastRunDate, 0, false) ;
//        $logging->log("Real Next from the object is {$realNextRun->format('d/m/Y H:i:00')}") ;
        $prevRun = $cron->getPreviousRunDate($lastRunDate, 0, true) ;
//        $logging->log("Previous from the object is {$prevRun->format('d/m/Y H:i:00')}") ;
        $isDue = $this->scheduleIsDue($lastRunDate, $prevRun, $realNextRun) ;
//        $logging->log("Is this schedule due is: {$isDue}") ;
        $sr = ($isDue==true) ? "yes" : "no" ;
//        $logging->log("Should this run is {$sr}") ;
        if ($isDue) { return true ; }
        return false ;
    }

    protected function scheduleIsDue($lastRunDate, $prevRun, $nextRun) {
        $loggingFactory = new \Model\Logging();
        $params["app-log"] = true ;
        $logging = $loggingFactory->getModel($params);
        $nowDate = new \DateTime() ;
        $nowDate->setTimestamp(time()) ;
//        @todo the below log calls as log level of verbose
//        $logging->log("Checking due date. For it to be due, the last run of {$lastRunDate->format('d/m/Y H:i:00')}") ;
//        $logging->log("compared to right now {$nowDate->format('d/m/Y H:i:00')}") ;
//        $logging->log("Must be equal to or greater than the amount of time between") ;
//        $logging->log("The previous scheduled run of {$prevRun->format('d/m/Y H:i:00')}") ;
//        $logging->log("compared to the next scheduled run {$nextRun->format('d/m/Y H:i:00')}") ;
        $prevt = $prevRun->getTimestamp() ;
        $nextt = $nextRun->getTimestamp() ;
        $diff_cron = $nextt - $prevt ;
        $lrt = $lastRunDate->getTimestamp() ;
        $nowt = $nowDate->getTimestamp() ;
        $diff_exec = $nowt - $lrt ;
//        @todo the below log calls as log level of verbose
//        $logging->log("Difference is {$diff_cron} seconds from cron") ;
//        $logging->log("Difference is {$diff_exec} seconds from real example") ;
        return $diff_exec > $diff_cron ;
    }

    private function libLoader() {
        $al = dirname(__DIR__).DS.'Libraries'.DS .'cron-expression-master'.DS.'vendor'.DS.'autoload.php' ;

        $loggingFactory = new \Model\Logging();
        $params["app-log"] = true ;
        $logging = $loggingFactory->getModel($params);
//        $logging->log("SSR libload file: {$al}") ;
        if (file_exists($al)) {
            require_once($al) ;}
        else {

            $logging->log("Unable to load Cron Library", $this->getModuleName(), LOG_FAILURE_EXIT_CODE) ;
        }
    }

    public function getPipelinesWithScheduledTasks() {
        $allPipelines = $this->getPipelines() ;

        $loggingFactory = new \Model\Logging();
        $params["app-log"] = true ;
        $logging = $loggingFactory->getModel($params);

        $pst = array() ;
        foreach ($allPipelines as $onePipeline) {
            //@todo this should not be tied to only poll scm, so that we can cron/etc builds without polling
            if (isset($onePipeline["settings"]["PollSCM"]["enabled"]) &&
                $onePipeline["settings"]["PollSCM"]["enabled"] == "on") {
                $logging->log("Pipeline '{$onePipeline["project-name"]}' includes Scheduled Task, of type Poll SCM.") ;
                $pst[] = array ("PollSCM", $onePipeline) ; }
            else if (isset($onePipeline["settings"]["ScheduledBuild"]["enabled"]) &&
                $onePipeline["settings"]["ScheduledBuild"]["enabled"] == "on") {
                $logging->log("Pipeline '{$onePipeline["project-name"]}' includes Scheduled Task, of type Scheduled Build.") ;
                $pst[] = array ("ScheduledBuild", $onePipeline) ; } }

        return $pst;
    }

    public function getPipelines() {
        $pipelineFactory = new \Model\Pipeline() ;
        $pipeline = $pipelineFactory->getModel($this->params);
        return $pipeline->getPipelines();
    }

    public function getPipeline($item) {
        $pipelineFactory = new \Model\Pipeline() ;
        $pipeline = $pipelineFactory->getModel($this->params);
        return $pipeline->getPipeline($item);
    }

    private function isWebSapi() {
        if (!in_array(PHP_SAPI, array("cli")))  { return true ; }
        return false ;
    }

}
