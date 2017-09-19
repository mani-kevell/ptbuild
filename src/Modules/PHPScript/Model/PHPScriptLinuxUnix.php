<?php

Namespace Model;

class PHPScriptLinuxUnix extends Base {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("Default") ;

    public function getStepTypes() {
        return array_keys($this->getFormFields());
    }

    public function getFormFields() {
        $ff = array(
            "phpscriptdata" => array(
                "type" => "textarea",
                "name" => "PHPScript Data",
                "slug" => "data" ),
            "phpscriptscript" => array(
                "type" => "text",
                "name" => "PHPScript Script",
                "slug" => "script" ),
        );

        return $ff ;
    }

    public function executeStep($step) {
        $loggingFactory = new \Model\Logging();
        $logging = $loggingFactory->getModel($this->params);
        if ( $step["steptype"] == "phpscriptdata") {
            $logging->log("Running PHPScript from Data...", $this->getModuleName()) ;
            $res = $this->executeAsPHPData($step["data"]) ;
//            var_dump("res", $res) ;
            return $res ; }
        else if ($step["steptype"] == "phpscriptfile") {
            $logging->log("Running PHPScript from Script...", $this->getModuleName()) ;
            $res = $this->executeAsPHPScript($step["data"]) ;
            return $res ; }
        else {
            $logging->log("Unrecognised Build Step Type {$step["type"]} specified in PHPScript Module", $this->getModuleName()) ;
            return false ; }
    }

    private function executeAsPHPData($data) {

        $data = str_replace("\r\n", "\n", $data) ;

        $loggingFactory = new \Model\Logging();
        $logging = $loggingFactory->getModel($this->params);
        $phpc = ''  ;
        if (isset($this->params["env-vars"]) && is_array($this->params["env-vars"])) {
            $loggingFactory = new \Model\Logging();
            $logging = $loggingFactory->getModel($this->params);
            $logging->log("PHP Extracting Environment Variables...", $this->getModuleName()) ;
            $ext_vars = json_encode($this->params["env-vars"], JSON_PRETTY_PRINT) ;
            $phpc .= '<?'.'php'."\n" ;
            $phpc .= '  '."\n" ;
            $phpc .= '  $extract_vars = \''.$ext_vars.'\';'."\n" ;
            $phpc .= '  $extract_vars_array = json_decode($extract_vars, true);'."\n" ;
            $phpc .= '  $extract_vars_keys = array_keys($extract_vars_array);'."\n" ;
            $phpc .= '  $extract_vars_keys_string = implode(",", $extract_vars_keys);'."\n" ;
            $phpc .= '  extract($extract_vars_array); '."\n" ;
            $phpc .= '  $count = count($extract_vars_array); '."\n" ;
            $phpc .= '  echo "PHP Successfully Extracted {$count} Environment Variables into PHP Variables {$extract_vars_keys_string}..." ; '."\n" ;
            $phpc .= ' '."\n" ; }
        $phpc .= $data  ;
        $tempFile = getcwd().DS.PHARAOH_APP."-temp-script-".mt_rand(100, 99999999999).".php";
        $stored = file_put_contents($tempFile, $phpc) ;
        if ($stored === false) {
            $logging->log("File not found, error...", $this->getModuleName(), LOG_FAILURE_EXIT_CODE);
            return false ; }
        $res = $this->executeAsPHPScript($tempFile) ;
        unlink($tempFile);
        return $res ;
    }

    private function executeAsPHPScript($scr_loc) {
        $loggingFactory = new \Model\Logging();
        $logging = $loggingFactory->getModel($this->params);
        if (file_exists($scr_loc)) {
            $logging->log("php $scr_loc", $this->getModuleName()) ;
            $comm = "{$scr_loc}" ;
            $res = $this->executePHP($comm, true, null) ;
            return ($res["rc"] === 0) ? true : false ; }
        else {
            $logging->log("File not found, error...", $this->getModuleName()) ;
            \Core\BootStrap::setExitCode(1);
            return false ;}
    }

}