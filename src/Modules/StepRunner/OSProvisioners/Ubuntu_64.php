<?php

Namespace Model ;

class StepRunner extends StepRunnerDefaultLinux {

    public $ostype = "Ubuntu 64 or 32 Bit from 10.04 onwards" ;

    public function getCleopatraInitSSHData($stepRunnerFile) {
		$sshData = "" ;
        $sshData .= "echo ".$this->phlagrantfile->config["ssh"]["password"]." | sudo -S apt-get update -y\n" ;
        $sshData .= "echo ".$this->phlagrantfile->config["ssh"]["password"]." | sudo -S apt-get install -y php5 git\n" ;
        $sshData .= "echo ".$this->phlagrantfile->config["ssh"]["password"]." | sudo -S rm -rf cleopatra\n" ;
        $sshData .= "echo ".$this->phlagrantfile->config["ssh"]["password"]." | sudo -S git clone https://github.com/PharaohTools/cleopatra.git\n" ;
        $sshData .= "echo ".$this->phlagrantfile->config["ssh"]["password"]." | sudo -S php cleopatra/install-silent\n" ;
        return $sshData ;
    }

    public function getMountSharesSSHData($stepRunnerFile) {
        $sshData = "" ;
        $sshData .= "echo {$this->phlagrantfile->config["ssh"]["password"]} "
            .'| sudo -S ln -s /opt/VBoxGuestAdditions-4.3.10/lib/VBoxGuestAdditions /usr/lib/VBoxGuestAdditions'."\n" ;
        foreach ($this->phlagrantfile->config["vm"]["shared_folders"] as $sharedFolder) {
            $guestPath = (isset($sharedFolder["guest_path"])) ? $sharedFolder["guest_path"] : $sharedFolder["host_path"] ;
            // @todo might be better not to sudo this creation, or allow it more params (owner, perms)
            $sshData .= "echo {$this->phlagrantfile->config["ssh"]["password"]} "
                .'| sudo -S mkdir -p '.$guestPath."\n" ;
            $sshData .= "echo {$this->phlagrantfile->config["ssh"]["password"]} "
                . '| sudo -S mount -t vboxsf ' . $sharedFolder["name"].' '.$guestPath.' '."\n" ; }
        return $sshData ;
    }

    public function getStandardCleopatraSSHData($stepRunnerFile, $params = array() ) {
        $paramString = "" ;
        foreach ($params as $paramKey => $paramValue) { $paramString .= " --$paramKey=$paramValue" ;}
        $sshData = <<<"SSHDATA"
echo {$this->phlagrantfile->config["ssh"]["password"]} | sudo -S cleopatra auto x --af={$stepRunnerFile}{$paramString}
SSHDATA;
        return $sshData ;
    }

    public function getStandardDapperstranoSSHData($stepRunnerFile, $params = array() ) {
        $paramString = "" ;
        foreach ($params as $paramKey => $paramValue) { $paramString .= " --$paramKey=$paramValue" ;}
        $sshData = <<<"SSHDATA"
echo {$this->phlagrantfile->config["ssh"]["password"]} | sudo -S dapperstrano auto x --af={$stepRunnerFile}{$paramString}
SSHDATA;
        return $sshData ;
    }

    public function getStandardShellSSHData($stepRunnerFile) {
        $sshData = <<<"SSHDATA"
echo {$this->phlagrantfile->config["ssh"]["password"]} | sudo -S sh $stepRunnerFile
SSHDATA;
        return $sshData ;
    }

}