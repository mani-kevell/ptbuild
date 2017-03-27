<?php

Namespace Model;

class BuildQueueAllOS extends Base {

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
            "enabled" => array(
                "type" => "boolean",
                "optional" => true,
                "name" => "Enable Queueing for this pipeline?"
            ),
            "build_queue_max" => array(
                "type" => "text",
                "optional" => true,
                "name" => "Max amount of builds to queue?"
            ),
            "build_queue_delay" => array(
                "type" => "text",
                "optional" => true,
                "name" => "Enter minutes to delay between builds?"
            ),
        );
        return $ff ;
    }

    public function getEventNames() {
        return array_keys($this->getEvents());
    }

    public function getEvents() {
        $ff = array(
            "buildQueueEnable" => array( "checkIfBuildRunRequiresQueue", ),
            "prepareBuild" => array( "checkBuildQueue", ),
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

    public function checkIfBuildRunRequiresQueue() {

        ob_start() ;
        var_dump("method to check if build requires queueing") ;
        var_dump("all params: ", $this->params) ;
//        debug_print_backtrace() ;
        $out = ob_get_clean() ;
        file_put_contents('/tmp/pharaoh.log', "build queueAllOS->checkIfBuildRunRequiresQueue() is executing: $out", FILE_APPEND) ;

        if (isset($this->params['build-settings']['BuildQueue']['enabled'])) {
            // build_queue_max
            // build_queue_delay
            // is this build already running?
            $pipeRunnerFactory = new \Model\PipeRunner() ;
            $pipeRunnerRunning = $pipeRunnerFactory->getModel($this->params, "FindRunning") ;
            $runningBuilds = $pipeRunnerRunning->getData() ;

//            ob_start() ;
//            var_dump("these are the running builds: ", $runningBuilds) ;
//            $out = ob_get_clean() ;
//            file_put_contents('/tmp/pharaoh.log', "build queueAllOS->checkIfBuildRunRequiresQueue() is executing: $out", FILE_APPEND) ;

            $is_running = false ;
            foreach ($runningBuilds["running_builds"] as $runningBuild) {
                file_put_contents('/tmp/pharaoh.log', "rbitem: {$runningBuild['item']} item param: {$this->params['item']}", FILE_APPEND) ;
                if ($runningBuild['item'] === $this->params['item']) {
                    $is_running = true ; } }
            if ($is_running === true) {
                $res = $this->addBuildToQueue() ;
                file_put_contents('/tmp/pharaoh.log', "added build to queue, res is {$res}", FILE_APPEND) ;
                return $res ; }
            else {
                return false ; }
        }
        else {
            // @todo file_put_contents('/tmp/pharaoh.log', "Queing builds is not enabled", FILE_APPEND) ;
            // Queing builds is not enabled, it definitely does not require queueing
            return false ;
        }
    }

    public function addBuildToQueue() {
        $save_build_parameters = (isset($this->params['build-parameters'])) ?
            json_encode($this->params['build-parameters']) : null ;
        $queue_entry = array() ;
        $queue_entry['pipeline_slug'] = $this->params['item'] ;
        $queue_entry['entry_time'] = time() ;
        $queue_entry['parameters'] = $save_build_parameters ;
        $queue_entry['settings'] = json_encode($this->params['build-settings']) ;
        $this->ensureDataCollection() ;
        $datastoreFactory = new \Model\Datastore() ;
        $datastore = $datastoreFactory->getModel($this->params) ;
        $res = $datastore->insert('build_queue', $queue_entry) ;
        return ($res === true) ? $queue_entry : false ;
    }

    public function findQueued() {
        $queue_entry = array() ;
        $queue_entry['pipeline_slug'] = $this->params['item'] ;
        $datastoreFactory = new \Model\Datastore() ;
        $datastore = $datastoreFactory->getModel($this->params) ;
        $res = $datastore->findAll('build_queue', $queue_entry) ;
        foreach ($res as &$one) {
            $one['entry_time_format'] = date('H:i:s d/m/Y', $one['entry_time']) ;
        }
        return $res ;
    }

    protected function ensureDataCollection() {
        $datastoreFactory = new \Model\Datastore() ;
        $datastore = $datastoreFactory->getModel($this->params) ;
        $loggingFactory = new \Model\Logging() ;
        $logging = $loggingFactory->getModel($this->params) ;
        if ( $datastore->collectionExists('build_queue') === true) {
            return true ;
        }
        $column_defines = array(
            'entry_id' => 'INTEGER PRIMARY KEY ASC',
            'pipeline_slug' => 'string',
            'entry_time' => 'string',
            'parameters' => 'string',
            'settings' => 'string',
        );
        $logging->log("Creating Build Queue Collection in Datastore", $this->getModuleName()) ;
        $res = $datastore->createCollection('build_queue', $column_defines) ;
        return $res ;
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


}
