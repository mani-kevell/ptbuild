<?php

Namespace Model;

class PublishStatusAPIAllOS extends Base {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("API") ;

    public function allowedFunctions() {
        return array('get_status') ;
    }

    public function get_status() {

        // load repo
        $this->params['item'] = $this->params['slug'] ;
        $pbsf = new \Model\PublishStatus() ;
        $pbs = $pbsf->getModel($this->params) ;
        $pipeline = $pbs->getPipeline() ;
        // if repo settings include

        if ($pipeline['last_status'] == true) {
            $status_string = 'success' ;
            $run_id = $pipeline['last_success_build'] ;
            $run_time = $pipeline['last_success'] ;
        } else if ($pipeline['last_status'] == false) {
            $status_string = 'failure' ;
            $run_id = $pipeline['last_fail_build'] ;
            $run_time = $pipeline['last_fail'] ;
        } else if ($pipeline['last_status'] == null) {
            $status_string = 'unknown' ;
            $run_id = $pipeline['last_run_build'] ;
            $run_time = $pipeline['last_run_start'] ;
        }

        return array(
            'status' => $status_string,
            'run_id' => $run_id,
            'build_job_title' => $pipeline['project-name'],
            'build_job_link' => 'http://'.$_SERVER["SERVER_NAME"].'/index.php?control=BuildHome&action=show&item='.$this->params['slug'],
            'build_run_link' => 'http://'.$_SERVER["SERVER_NAME"].'/index.php?control=PipeRunner&action=summary&item=script_testing&run-id='.$run_id,
            'build_run_time' => $run_time,
        ) ;


    }

}
