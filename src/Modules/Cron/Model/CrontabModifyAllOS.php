<?php

Namespace Model;

class CrontabModifyAllOS extends Base {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("CrontabModify") ;

    public function addCronjob($module) {
        $cronCmd = $this->getCronCommand($module);
        $loggingFactory = new \Model\Logging();
        $this->params["php-log"] = true ;
        $logging = $loggingFactory->getModel($this->params);
        $cronJobs = $this->getCronjobs() ;
        if (!in_array($cronCmd, $cronJobs)) {
            $cronJobs[] = $cronCmd ;
        }
        return $this->saveCronjobs($cronJobs) ;
    }

    public function removeCronjob($module) {
        $loggingFactory = new \Model\Logging();
        $this->params["php-log"] = true ;
        $logging = $loggingFactory->getModel($this->params);
        $cronJobs = $this->getCronjobs() ;
        $cronJobSave = array() ;
        if (count($cronJobs)==0) {
            $logging->log ("Nothing to remove! The crontab is already empty.", $this->getModuleName() ) ;
            return true ; }
        for ($i=0; $i<count($cronJobs); $i++) {
            if ($cronJobs[$i] =="") {
                $logging->log ("Removing Empty Cron entry", $this->getModuleName() ) ;
                unset($cronJobs[$i]) ; }
            else if (strpos($cronJobs[$i], $this->params["app-settings"][$module]["cron_command"])) {
                $logging->log ("Removing Cron entry $cronJobs[$i]", $this->getModuleName() ) ;
                unset($cronJobs[$i]) ; }
            else { $cronJobSave[] = $cronJobs[$i] ; }}
        $this->saveCronjobs($cronJobSave) ;
    }

    private function getCronjobs() {
        $cronJobs = explode("\n", self::executeAndLoad("crontab -l"));
        return $cronJobs;
    }
    private function getCronCommand($module) {
        $cronCmd = $this->params["app-settings"][$module]["cron_frequency"].' '.$this->params["app-settings"][$module]["cron_command"] ;
        return $cronCmd;
    }

    private function saveCronjobs($cj) {
//        $fpc = file_put_contents("/tmp/crontemp.txt", implode("\n", $cj)."\n");
//        var_dump('fpc', $fpc) ;
        $out = self::executeAndGetReturnCode("crontab < /tmp/crontemp.txt", true, true) ;
//        var_dump('cront out', $out) ;
        return ($out['rc'] === 0) ? true : false ;
    }

}