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
            "afterBuildTriggers" => array( "consumeQueues", ),
            "afterApplicationConfigurationSave" => array("crontabBuildQueue",),
        );
        return $ff ;
    }

    public function crontabBuildQueue() {
//        var_dump("running crontab parent") ;
        $loggingFactory = new \Model\Logging();
        $this->params["php-log"] = true ;
        $this->params["echo-log"] = true ;
        $logging = $loggingFactory->getModel($this->params);
        $mn = $this->getModuleName() ;
        $this->params["app-settings"] = \Model\AppConfig::getAppVariable("mod_config");
        $logging->log("Creating Build Queue Crontab", $this->getModuleName()) ;

//        var_dump("about to") ;
        if ($this->params["app-settings"][$mn]["cron_enable"] === "on") {
//            var_dump("enabled") ;
            $cronFactory = new \Model\Cron();
            $cronModify = $cronFactory->getModel($this->params, "CrontabModify");
            $res = $cronModify->addCronjob("BuildQueue");
//            var_dump("enabled res :", $res) ;
//            die() ;
            return $res ; }
        else {
//            var_dump("disabled") ;
            $logging->log ("Cron disabled, deleting current crontab...", $this->getModuleName() ) ;
            $cronFactory = new \Model\Cron();
            $cronModify = $cronFactory->getModel($this->params, "CrontabModify");
            $res = $cronModify->removeCronjob("BuildQueue");
//            var_dump("disabled res :", $res) ;
//            die() ;
            return $res ; }
    }

    public function checkIfBuildRunRequiresQueue() {

//        ob_start() ;
//        var_dump("method to check if build requires queueing") ;
//        var_dump("all params: ", $this->params) ;
////        debug_print_backtrace() ;
//        $out = ob_get_clean() ;
//        file_put_contents('/tmp/pharaoh.log', "build queueAllOS->checkIfBuildRunRequiresQueue() is executing: $out", FILE_APPEND) ;

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
//                file_put_contents('/tmp/pharaoh.log', "rbitem: {$runningBuild['item']} item param: {$this->params['item']}", FILE_APPEND) ;
                if ($runningBuild['item'] === $this->params['item']) {
                    $is_running = true ; } }
            if ($is_running === true) {
                $res = $this->addBuildToQueue() ;
//                file_put_contents('/tmp/pharaoh.log', "added build to queue, res is {$res}", FILE_APPEND) ;
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
            $this->params['build-parameters'] : null ;

        if (is_array($save_build_parameters)) {
            $save_build_parametersx = json_encode($save_build_parameters) ;
        } else if (is_object($save_build_parameters)) {
            $save_build_parametersx = json_encode($save_build_parameters) ;
        } else {
            $save_build_parameters2 = unserialize($save_build_parameters) ;
            $save_build_parametersx = $save_build_parameters3 = json_encode($save_build_parameters2) ;
        }

        $queue_entry = array() ;
        $queue_entry['pipeline_slug'] = $this->params['item'] ;
        $queue_entry['entry_time'] = time() ;
        $queue_entry['parameters'] = $save_build_parametersx ;
        $queue_entry['settings'] = json_encode($this->params['build-settings']) ;

        $this->ensureDataCollection() ;
        $datastoreFactory = new \Model\Datastore() ;
        $datastore = $datastoreFactory->getModel($this->params) ;
        $res = $datastore->insert('build_queue', $queue_entry) ;
        return ($res === true) ? $queue_entry : false ;
    }

    public function findQueued() {
        $queue_entry = array() ;
        if (isset($this->params['item'])) {
            $queue_entry['pipeline_slug'] = $this->params['item'] ;
        }
        $datastoreFactory = new \Model\Datastore() ;
        $datastore = $datastoreFactory->getModel($this->params) ;
        $res = $datastore->findAll('build_queue', $queue_entry) ;
        foreach ($res as &$one) {
            $one['entry_time_format'] = date('H:i:s d/m/Y', $one['entry_time']) ;
        }
        return $res ;
    }

    public function consumeQueues() {
        $bqFactory = new \Model\BuildQueue() ;
        $bq = $bqFactory->getModel($this->params, 'Consume');
//        $bq->ensureDataCollection() ;
        $res = $bq->getData() ;
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


}
