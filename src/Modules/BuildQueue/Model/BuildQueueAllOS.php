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
            "buildQueueEnable" => array("checkIfBuildRunRequiresQueue", ),
        );
        return $ff ;
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


            ob_start() ;
            var_dump("these are the running builds: ", $runningBuilds) ;
            $out = ob_get_clean() ;
            file_put_contents('/tmp/pharaoh.log', "build queueAllOS->checkIfBuildRunRequiresQueue() is executing: $out", FILE_APPEND) ;


            $is_running = false ;
            foreach ($runningBuilds["running_builds"] as $runningBuild) {
                file_put_contents('/tmp/pharaoh.log', "rbitem: {$runningBuild['item']} item param: {$this->params['item']}", FILE_APPEND) ;
                if ($runningBuild['item'] === $this->params['item']) {
                    $is_running = true ;
                }
            }

            if ($is_running === true) {
                $res = $this->addBuildToQueue() ;
                file_put_contents('/tmp/pharaoh.log', "added build to queue, res is {$res}", FILE_APPEND) ;
                return $res ;
            }
            else {
                return false ;
            }


        }
        else {
            file_put_contents('/tmp/pharaoh.log', "Queing builds is not enabled", FILE_APPEND) ;

            // @todo
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

}
