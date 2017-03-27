<?php

Namespace Model;

class BuildQueueConsumeAllOS extends Base {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("Consume") ;

    public function getData() {
        $ret["scheduled"] = $this->getPipelinesRequiringExecution();
        $ret["executions"] = $this->executePipes($ret["scheduled"]);
        return $ret ;
    }

    public function getPipelinesRequiringExecution() {
        $psts = $this->getPipelinesWithBuildQueues() ;
        $psrxs = array() ;
        $loggingFactory = new \Model\Logging();
        $params["app-log"] = true ;
        $logging = $loggingFactory->getModel($params);
        for ($i = 0; $i<count($psts) ; $i++) {
            $prx = $this->pipeRequiresExecution($psts[$i][1], $psts[$i][0]) ;
            if ($prx === true) {
                $psrxs[] = $psts[$i][1] ; } }
        return $psrxs;
    }

    public function getPipelinesWithBuildQueues() {
        $allPipelines = $this->getPipelines() ;
        $loggingFactory = new \Model\Logging();
        $params["app-log"] = true ;
        $logging = $loggingFactory->getModel($params);
        $pst = array() ;
        foreach ($allPipelines as $onePipeline) {
            //@todo this should not be tied to only poll scm, so that we can cron/etc builds without polling
            if (isset($onePipeline["settings"]["PollSCM"]["enabled"]) &&
                $onePipeline["settings"]["PollSCM"]["enabled"] === "on") {
                $lmsg = "Pipeline '{$onePipeline["project-name"]}' includes Build Queue" ;
                $logging->log($lmsg, $this->getModuleName()) ;
                $pst[] = array ("PollSCM", $onePipeline) ; } }
        return $pst;
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


}
