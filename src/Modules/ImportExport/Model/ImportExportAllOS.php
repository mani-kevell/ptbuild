<?php

Namespace Model;

class ImportExportAllOS extends Base {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("Default") ;

	public function importJob() {
        $loggingFactory = new \Model\Logging();
        $this->params["echo-log"] = true ;
        $logging = $loggingFactory->getModel($this->params);

        $dir = $this->params["source"] ;
        if ($dir == "") {
            $logging->log("Unable to import job - no directory has been specified.", $this->getModuleName(), LOG_FAILURE_EXIT_CODE);
            return false ; }

        $dir = $this->ensureTrailingSlash($dir) ;

        $job_dir_name = basename($dir) ;
        $logging->log("Importing {$job_dir_name} from {$dir}...", $this->getModuleName());

        $target_dir = PIPEDIR.DS.$job_dir_name ;
        $target_dir = $this->ensureTrailingSlash($target_dir) ;

        if (!is_dir($target_dir)) {
            $logging->log("Creating target directory {$target_dir}", $this->getModuleName());
            $res = mkdir($target_dir,0775, true) ;
            if ($res == false) {
                $logging->log("Creating New Build Job Directory unsuccessful", $this->getModuleName(), LOG_FAILURE_EXIT_CODE);
                return false ;
            }
        } else {
            $logging->log("Target directory {$target_dir} already exists", $this->getModuleName());
        }


        $files_to_copy = array("settings", "steps", "defaults");
        foreach ($files_to_copy as $file_to_copy) {
            $source = $dir.$file_to_copy ;
            $target = $target_dir.$file_to_copy ;
            $logging->log("Copying {$source} to {$target}", $this->getModuleName());
            $copy_command = "cp {$source} {$target}" ;
            $rc = $this->executeAndGetReturnCode($copy_command, false, true) ;
            if ($rc["rc"] !== 0) {
                $last = count($rc["output"])-1 ;
                $logging->log("File Copy unsuccessful, Error: {$rc["output"][$last]}", $this->getModuleName(), LOG_FAILURE_EXIT_CODE);
                return false ;
            }
        }


        $files_to_touch = array("historyIndex");
        foreach ($files_to_touch as $file_to_touch) {
            $logging->log("Touching $file_to_touch", $this->getModuleName());
            $filename = $dir.$file_to_touch ;
            $res = touch($filename) ;
            if ($res == false) {
                $logging->log("File Touch unsuccessful for ", $this->getModuleName(), LOG_FAILURE_EXIT_CODE);
                return false ;
            }
        }

        $dirs_to_ensure = array("history", "tmp", "workspace");
        foreach ($dirs_to_ensure as $dir_to_ensure) {
            $target = $target_dir.$dir_to_ensure ;
            if (is_dir($target)) {
                $logging->log("Target dir {$target} already exists", $this->getModuleName());
                continue ;
            }
            else {
                $logging->log("Creating Job sub directory {$target}", $this->getModuleName());
                $res = mkdir($target,0775, true) ;
                if ($res == false) {
                    $logging->log("Creating Job Subdirectory Directory unsuccessful", $this->getModuleName(), LOG_FAILURE_EXIT_CODE);
                    return false ;
                }
            }
        }

        $copy_command = "chmod -R 775 {$target_dir}" ;
        $rc = $this->executeAndGetReturnCode($copy_command, false, true) ;
        if ($rc["rc"] !== 0) {
            $last = count($rc["output"])-1 ;
            $logging->log("Changing Mode to 775 Failed, Error: {$rc["output"][$last]}", $this->getModuleName(), LOG_FAILURE_EXIT_CODE);
            return false ;
        }
        else {
            $logging->log("Changing Mode Successful", $this->getModuleName());
        }

        $copy_command = "chown -R ptbuild:ptbuild {$target_dir}" ;
        $rc = $this->executeAndGetReturnCode($copy_command, false, true) ;
        if ($rc["rc"] !== 0) {
            $last = count($rc["output"])-1 ;
            $logging->log("Changing Ownership to ptbuild Failed, Error: {$rc["output"][$last]}", $this->getModuleName(), LOG_FAILURE_EXIT_CODE);
            return false ;
        }
        else {
            $logging->log("Changing Ownership Successful", $this->getModuleName());
        }

        return true ;

    }

}
