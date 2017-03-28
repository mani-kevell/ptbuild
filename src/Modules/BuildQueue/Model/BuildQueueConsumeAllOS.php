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
        $ret["queued"] = $this->getPipelinesRequiringExecution();
        $ret["executions"] = $this->executePipes($ret["queued"]);
        return $ret ;
    }

    public function getPipelinesRequiringExecution() {
        $psts = $this->getPipelinesWithBuildQueues() ;
        $psrxs = array() ;
        $loggingFactory = new \Model\Logging();
        $params["app-log"] = true ;
        $logging = $loggingFactory->getModel($params);
        for ($i = 0; $i < count($psts) ; $i++) {
            $prx = $this->pipeRequiresExecution($psts[$i]) ;
            if ($prx !== false) {
                $psrxs[] = array($psts[$i], $prx) ; } }
        return $psrxs;
    }

    public function getPipelinesWithBuildQueues() {

        $pipelineFactory = new \Model\Pipeline();
        $pmod = $pipelineFactory->getModel($this->params, 'PipelineRepository') ;
        $allPipelines = $pmod->getAllPipelines() ;
        $loggingFactory = new \Model\Logging();
        $params["app-log"] = true ;
        $logging = $loggingFactory->getModel($params);
        $pst = array() ;
        foreach ($allPipelines as $onePipeline) {
            //@todo this should not be tied to only poll scm, so that we can cron/etc builds without polling
            if (isset($onePipeline["settings"]["BuildQueue"]["enabled"]) &&
                $onePipeline["settings"]["BuildQueue"]["enabled"] === "on") {
                $lmsg = "Pipeline '{$onePipeline["project-name"]}' includes Build Queue" ;
                $logging->log($lmsg, $this->getModuleName()) ;
                $pst[] = $onePipeline ; } }
        return $pst;
    }

    private function executePipes($pipes) {
        $prFactory = new \Model\PipeRunner() ;
        $results = array();
        foreach ($pipes as $pipe_and_queue) {
            $params = $this->params ;
            $params["item"] = $pipe_and_queue[0]["project-slug"] ;
            $params["build-settings"] = $pipe_and_queue[1]["settings"] ;
            if (isset($pipe_and_queue[1]["settings"]) && $pipe_and_queue[1]["settings"] !== null) {
                $params["build-parameters"] = $pipe_and_queue[1]["parameters"] ;
            }
            $pr = $prFactory->getModel($params) ;
            $results[$pipe_and_queue[0]["project-slug"]] =
                array(
                    "name" => $pipe_and_queue[0]["project-name"],
                    "result" => $pr->runPipe()
                ) ;
            $this->removeBuildFromQueue($pipe_and_queue[1]["entry_id"]) ; }
        return $results ;
    }


    public function removeBuildFromQueue($entry_id) {
        $queue_entry = array() ;
        $queue_entry['entry_id'] = $entry_id ;
//        $bqFactory = new \Model\BuildQueue() ;
//        $bq = $bqFactory->getModel($this->params);
//        $bq->ensureDataCollection() ;
        $datastoreFactory = new \Model\Datastore() ;
        $datastore = $datastoreFactory->getModel($this->params) ;
        $res = $datastore->delete('build_queue', $queue_entry) ;
        return ($res === true) ? $queue_entry : false ;
    }

    // @todo we need to check multiple modules and return true if any are true, we should also
    // @todo say which one of the mods is tru
    public function pipeRequiresExecution($pst) {
        $loggingFactory = new \Model\Logging();
        $params["app-log"] = true ;
        $logging = $loggingFactory->getModel($params);
        $mod = $this->getModuleName() ;
        if (isset($pst["settings"][$mod]["enabled"]) && $pst["settings"][$mod]["enabled"] === "on") {
            $logging->log("Checking if Pipeline '{$pst["project-name"]}' is queued", $this->getModuleName()) ;
            $bqFactory = new \Model\BuildQueue() ;
            $bq = $bqFactory->getModel($params);
            $queued = $bq->findQueued() ;
//            var_dump($queued) ;
            foreach ($queued as $one_entry) {
                if ($pst["project-slug"] === $one_entry['pipeline_slug']) {
                    return $one_entry ; } } }
        return false ;
    }


}
